<?php
require_once dirname(__FILE__).'/database_connect.php';
require_once dirname(__FILE__).'/../entity/BidRule.php';

class BidRuleDAO {
    function load($id) {
        $conn = get_conn();

        $query = "SELECT * FROM bid_rule WHERE id = $id ";

        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $row = mysql_fetch_array($result);
        $bidRule = $this->instantiateBidRule($row);

        close_conn($conn);
        return $bidRule;
    }

    function instantiateBidRule($row) {
        if(isset($row["id"])) {
            $rule = new BidRule();
            $rule->id = $row["id"];
            $rule->entityId = $row["ppc_entity_id"];
            $rule->entityType = $row["ppc_entity_type"];
            $rule->ruleType = $row["rule_type"];
            $rule->cost_threshold = $row["cost_threshold"];
            $rule->increase_percent = $row["increase_percent"];
            $rule->increase_days = $row["increase_days"];
            $rule->decrease_percent = $row["decrease_percent"];
            $rule->decrease_days = $row["decrease_days"];
            $rule->apply = $row["apply"] > 0;
            return $rule;
        }
        return null;
    }

    function save($rule) {
        $conn = get_conn();

        $cost_threshold = mysql_real_escape_string($rule->cost_threshold);
        $increase_percent = mysql_real_escape_string($rule->increase_percent);
        $increase_days = mysql_real_escape_string($rule->increase_days);
        $decrease_percent = mysql_real_escape_string($rule->decrease_percent);
        $decrease_days = mysql_real_escape_string($rule->decrease_days);
        $apply = ($rule->apply) ? 1 : 0;

        $query = "
    INSERT INTO bid_rule (
        id,
        ppc_entity_id,
        ppc_entity_type,
        rule_type,
        cost_threshold,
        increase_percent,
        increase_days,
        decrease_percent,
        decrease_days,
        apply
        )
    VALUES (
            {$rule->id},
            {$rule->entityId},
            {$rule->entityType},
            {$rule->ruleType},
            $cost_threshold,
            $increase_percent,
            $increase_days,
            $decrease_percent,
            $decrease_days,
            $apply
        )
      ON DUPLICATE KEY UPDATE
        id=LAST_INSERT_ID(id),
        ppc_entity_id = VALUES(ppc_entity_id),
        ppc_entity_type = VALUES(ppc_entity_type),
        rule_type = VALUES(rule_type),
        cost_threshold = VALUES(cost_threshold),
        increase_percent = VALUES(increase_percent),
        increase_days = VALUES(increase_days),
        decrease_percent = VALUES(decrease_percent),
        decrease_days = VALUES(decrease_days),
        apply = VALUES(apply)
            ";

        mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());
        $rule->id = mysql_insert_id();
        close_conn($conn);
        return $rule;
    }
}
?>
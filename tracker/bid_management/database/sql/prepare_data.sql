INSERT INTO bid_management_data(
    campaign_id,
    adgroup_id,
    keyword_id,
    data_date,
    revenue,
    cost
    )

SELECT
    r.campaign_id,
    r.adgroup_id,
    r.keyword_id,
    r.data_date,
    r.revenue,
    c.cost

FROM
    (SELECT
        pc.id campaign_id,
        pa.id adgroup_id,
        pk.id keyword_id,
        FROM_UNIXTIME(kl.timestamp, '%Y-%m-%d') data_date,
        SUM(kl.revenue) revenue,
        kl.cost_updated,
        kl.revenue_updated
    FROM
        ppc_campaigns pc LEFT JOIN kw_log kl ON
            pc.campaign_name = kl.campaign AND
            pc.engine = kl.source LEFT JOIN
        ppc_adgroups pa ON
            pa.adgroup_name = kl.adgroup AND
            pa.campaign_id = pc.id LEFT JOIN
        ppc_keywords pk ON
            pk.text = kl.keyword AND
            pk.match_type = kl.match_type AND
            pk.adgroup_id = pa.id
    GROUP BY
        campaign_id,
        adgroup_id,
        keyword_id,
        data_date
    ) r LEFT JOIN
                    
    (SELECT
        pc.id campaign_id,
        pa.id adgroup_id,
        pk.id keyword_id,
        FROM_UNIXTIME(c.int_date, '%Y-%m-%d') data_date,
        SUM(c.total_cost) cost
    FROM
        ppc_campaigns pc LEFT JOIN cost c ON
            pc.campaign_name = c.campaign AND
            pc.engine = c.engine LEFT JOIN
        ppc_adgroups pa ON
            pa.adgroup_name = c.adgroup AND
            pa.campaign_id = pc.id LEFT JOIN
        ppc_keywords pk ON
            pk.text = c.keyword AND
            pk.match_type = c.match_type AND
            pk.adgroup_id = pa.id
    GROUP BY
        campaign_id,
        adgroup_id,
        keyword_id,
        data_date) c ON
        r.campaign_id = c.campaign_id AND
        r.adgroup_id = c.adgroup_id AND
        r.keyword_id = c.keyword_id AND
        r.data_date = c.data_date

WHERE
    r.cost_updated > 0 AND r.revenue_updated > 0

ON DUPLICATE KEY UPDATE
    campaign_id=VALUES(campaign_id),
    adgroup_id=VALUES(adgroup_id),
    keyword_id=VALUES(keyword_id),
    data_date=VALUES(data_date),
    revenue=VALUES(revenue),
    cost=VALUES(cost)

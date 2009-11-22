<?php
  // import this file only when we are not in OO mode
  // however, if we are in OO mode, the import happens in APIlityUser.php  
  if (!IS_ENABLED_OO_MODE) {
    require_once('Criterion.inc.php');  
  }

  /*
   SUPER CLASS FOR CRITERION
  */

  class APIlityCriterion {
    // class attributes
    var $belongsToAdGroupId;
    var $criterionType;
    var $destinationUrl;
    var $id;
    var $language;
    var $isNegative;
    var $isPaused;
    var $status;

    // constructor
    function APIlityCriterion(
        $id,
        $belongsToAdGroupId,
        $criterionType,
        $isNegative,
        $isPaused,
        $status,
        $language,
        $destinationUrl
    ) {
      $this->id = $id;
      $this->belongsToAdGroupId = $belongsToAdGroupId;
      $this->criterionType = $criterionType;
      $this->status = $status;
      $this->language = $language;
      $this->destinationUrl = $destinationUrl;
      $this->isPaused = convertBool($isPaused);
      $this->isNegative = convertBool($isNegative);
    }

    // get functions
    function getBelongsToAdGroupId() {
      return $this->belongsToAdGroupId;
    }

    function getCriterionType() {
      return $this->criterionType;
    }

    function getDestinationUrl() {
      return $this->destinationUrl;
    }

    function getId() {
      return $this->id;
    }

    function getLanguage() {
      return $this->language;
    }

    function getIsNegative() {
      return $this->isNegative;
    }

    function getIsPaused() {
      return $this->isPaused;
    }

    function getStatus() {
      return $this->status;
    }

    function getCriterionStats($startDate, $endDate) {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      $soapParameters = "<getCriterionStats>
                           <adGroupId>" . 
                             $this->getBelongsToAdGroupId() . "
                           </adGroupId>
                           <criterionIds>" . $this->getId() . "</criterionIds>
                           <startDay>" . $startDate . "</startDay>
                           <endDay>" . $endDate . "</endDay>
                         </getCriterionStats>";
      // get criterion stats from the google servers
      $criterionStats = $someSoapClient->call("getCriterionStats", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getCriterionStats()", $soapParameters);
        return false;
      }
      // if we have a keyword criterion add keyword text to the returned stats
      // for the sake of clarity
      if (strcasecmp($this->criterionType, "Keyword") == 0)
          $criterionStats['getCriterionStatsReturn']['text'] = $this->getText();
      // transform micros to currency units
      $criterionStats['getCriterionStatsReturn']['cost'] =
          ((double) $criterionStats['getCriterionStatsReturn']['cost']) / EXCHANGE_RATE;
      return $criterionStats['getCriterionStatsReturn'];
    }

    // set functions
    function setLanguage($newLanguage) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      if (isset($this->text)) {
        $criterionType = "Keyword";
      }
      else {
        $criterionType = "Website";
      }
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      // danger! think in micros
      $soapParameters = "<updateCriteria>
                           <criteria>
                             <id>" . $this->getId() . "</id>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>" . $criterionType . "</criterionType>
                             <negative>" . $isNegative . "</negative>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                             <language>" . $newLanguage . "</language>
                           </criteria>
                         </updateCriteria>";
      // update the keyword on the google servers
      $someSoapClient->call("updateCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setLanguage()", $soapParameters);
        return false;
      }
      // update local object
      $this->language = $newLanguage;
      return true;
    }

    function setDestinationUrl($newDestinationUrl) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      if (isset($this->text)) {
        $criterionType = "Keyword";
      }
      else {
        $criterionType = "Website";
      }
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      $soapParameters = "<updateCriteria>
                           <criteria>
                             <id>" . $this->getId() . "</id>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>" . $criterionType . "</criterionType>
                             <negative>" . $isNegative . "</negative>
                             <destinationUrl>" . $newDestinationUrl . "</destinationUrl>
                           </criteria>
                         </updateCriteria>";
      // update the keyword on the google servers
      $someSoapClient->call("updateCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDestinationUrl()", $soapParameters);
        return false;
      }
      // update local object
      $this->destinationUrl = $newDestinationUrl;
      return true;
    }

    function setIsNegative($newFlag) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      if (isset($this->text)) {
        $criterionType = "Keyword";
      }
      else {
        $criterionType = "Website";
      }
      // make sure bool gets transformed into string correctly
      if ($newFlag) $newFlag = "true"; else $newFlag = "false";
      // danger! think in micros
      $soapParameters = "<updateCriteria>
                           <criteria>
                             <id>" . $this->getId() . "</id>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <negative>" . $newFlag . "</negative>
                             <criterionType>" . $criterionType . "</criterionType>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                           </criteria>
                         </updateCriteria>";
      // update the keyword on the google servers
      $someSoapClient->call("updateCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setIsNegative()", $soapParameters);
        return false;
      }
      // update local object
      $this->isNegative = convertBool($newFlag);
      return true;
    }

    function setIsPaused($newFlag) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      if (isset($this->text)) {
        $criterionType = "Keyword";
      }
      else {
        $criterionType = "Website";
      }
      // make sure bool gets transformed into string correctly
      if ($newFlag) $newFlag = "true"; else $newFlag = "false";
      $soapParameters = "<updateCriteria>
                           <criteria>
                             <id>" . $this->getId() . "</id>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <paused>" . $newFlag . "</paused>
                             <criterionType>" . $criterionType . "</criterionType>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                           </criteria>
                         </updateCriteria>";
      // update the keyword on the google servers
      $someSoapClient->call("updateCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setIsPaused()", $soapParameters);
        return false;
      }
      // update local object
      $this->isPaused = convertBool($newFlag);
      return true;
    }
  }

  /*
    KEYWORD CRITERION
  */

  class APIlityKeywordCriterion extends APIlityCriterion {
    // keyword class attributes
    var $text;
    var $maxCpc;
    var $proxyMaxCpc;
    var $firstPageCpc;
    var $qualityScore;
    var $type;

    // constructor
    function APIlityKeywordCriterion(
        $text,
        $id,
        $belongsToAdGroupId,
        $type,
        $criterionType,
        $isNegative,
        $isPaused,
        $maxCpc,
        $firstPageCpc,
        $proxyMaxCpc,
        $qualityScore,
        $status,
        $language,
        $destinationUrl
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityCriterion::APIlityCriterion(
        $id,
        $belongsToAdGroupId,
        $criterionType,
        $isNegative,
        $isPaused,
        $status,
        $language,
        $destinationUrl
      );
      // now construct the keyword criterion which inherits all other criterion
      // attributes
      $this->text = $text;
      $this->maxCpc =  $maxCpc;
      $this->firstPageCpc =  $firstPageCpc;
      $this->qualityScore = $qualityScore;
      $this->proxyMaxCpc = $proxyMaxCpc;
      $this->type = $type;
    }

    // XML output
    function toXml() {
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      if ($this->getIsPaused()) {
        $isPaused = "true";
      }
      else {
        $isPaused = "false";
      }
      $xml = "<KeywordCriterion>
  <text>" . xmlEscape($this->getText()) . "</text>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <type>" . $this->getType() . "</type>
  <criterionType>" . $this->getCriterionType() . "</criterionType>
  <isNegative>" . $isNegative . "</isNegative>
  <isPaused>" . $isPaused . "</isPaused>
  <status>" . $this->getStatus() . "</status>
  <maxCpc>" . $this->getMaxCpc() . "</maxCpc>
  <firstPageCpc>" . $this->getFirstPageCpc() . "</firstPageCpc>
  <qualityScore>" . $this->getQualityScore() . "</qualityScore>  
  <proxyMaxCpc>" . $this->getProxyMaxCpc() . "</proxyMaxCpc>
  <language>" . $this->getLanguage() . "</language>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
</KeywordCriterion>";
      return $xml;
    }

    // get functions
    function getText() {
      return $this->text;
    }

    function getMaxCpc() {
      return $this->maxCpc;
    }

    function getProxyMaxCpc() {
      return $this->proxyMaxCpc;
    }

    function getFirstPageCpc() {
      return $this->firstPageCpc;
    }

    function getQualityScore() {
      return $this->qualityScore;
    }

    function getType() {
      return $this->type;
    }

    function getCriterionData() {
      $criterionData = array(
                           'text' => $this->getText(),
                           'id' => $this->getId(),
                           'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                           'type' => $this->getType(),
                           'criterionType' => $this->getCriterionType(),
                           'isNegative' => $this->getIsNegative(),
                           'isPaused' => $this->getIsPaused(),
                           'maxCpc' => $this->getMaxCpc(),
                           'firstPageCpc' => $this->getFirstPageCpc(),
                           'qualityScore' => $this->getQualityScore(),
                           'proxyMaxCpc' => $this->getProxyMaxCpc(),
                           'status' => $this->getStatus(),
                           'language' => $this->getLanguage(),
                           'destinationUrl' => $this->getDestinationUrl()
                         );
      return $criterionData;
    }

    function getEstimate() {
      // this function is located in TrafficEstimate.php
      return getKeywordEstimate($this);
    }

    // set functions
    function setText($newText) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      // changing the text is not provided by the api so we need to emulate this
      // by removing and re-creating then re-create the keyword with the new
      // text set
      // make sure bool gets correctly transformed to string
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      // danger! we need to think in micros so we need to transform the object
      // maxcpc to micros
      $soapParameters = "<addCriteria>
                           <criteria>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>Keyword</criterionType>
                             <type>" . $this->getType() . "</type>
                             <text>" . $newText . "</text>
                             <negative>" . $isNegative . "</negative>
                             <maxCpc>" . 
                               $this->getMaxCpc() * EXCHANGE_RATE . "
                             </maxCpc>
                             <language>" . $this->getLanguage() . "</language>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                           </criteria>
                         </addCriteria>";
      // add criterion to the google servers
      $someCriterion = $someSoapClient->call("addCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setText()", $soapParameters);
        return false;
      }
      // first delete current keyword
      $soapParameters = "<removeCriteria>
                           <adGroupId>" . 
                             $this->getBelongsToAdGroupId() . "
                           </adGroupId>
                           <criterionIds>" . $this->getId() . "</criterionIds>
                         </removeCriteria>";
      // talk to the google servers
      $someSoapClient->call("removeCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setText()", $soapParameters);
        return false;
      }
      // update local object
      $this->text = $newText;
      // changing the text of a keyword will change its id, so update object id
      // data
      $this->id = $someCriterion['addCriteriaReturn']['id'];
      return true;
    }

    function setType($newType) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      // changing the type is not provided by the api so emulate this by
      // deleting and re-creating the keyword
      // then re-create the keyword with the new text set
      // make sure bool gets correctly transformed to string
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      // danger! we need to think in micros so we need to transform the object
      // maxcpc to micros
      $soapParameters = "<addCriteria>
                           <criteria>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>Keyword</criterionType>
                             <type>" . $newType . "</type>
                             <text>" . $this->getText() . "</text>
                             <negative>" . $isNegative . "</negative>
                             <maxCpc>" . 
                               $this->getMaxCpc() * EXCHANGE_RATE . "
                             </maxCpc>
                             <language>" . $this->getLanguage() . "</language>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                           </criteria>
                         </addCriteria>";
      // add criterion to the google servers
      $someCriterion = $someSoapClient->call("addCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setType()", $soapParameters);
        return false;
      }
      // first delete current keyword
      $soapParameters = "<removeCriteria>
                           <adGroupId>" . 
                             $this->getBelongsToAdGroupId() . "
                           </adGroupId>
                           <criterionIds>" . $this->getId() . "</criterionIds>
                        </removeCriteria>";
      // talk to the google servers
      $someSoapClient->call("removeCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setType()", $soapParameters);
        return false;
      }
      // update local object
      $this->type = $newType;
      // changing the type of a keyword will change its id, so update object
      // id data
      $this->id = $someCriterion['addCriteriaReturn']['id'];
      return true;
    }

    function setMaxCpc($newMaxCpc) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      // danger! think in micros
      $soapParameters = "<updateCriteria>
                           <criteria>
                             <id>" . $this->getId() . "</id>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>Keyword</criterionType>
                             <maxCpc>" . $newMaxCpc * EXCHANGE_RATE . "</maxCpc>
                             <negative>" . $isNegative . "</negative>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                           </criteria>
                         </updateCriteria>";
      // update the keyword on the google servers
      $someSoapClient->call("updateCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setMaxCpc()", $soapParameters);
        return false;
      }
      // update local object
      $this->maxCpc = $newMaxCpc;
      return true;
    }
  }

  /*
    WEBSITE CRITERION
  */

  class APIlityWebsiteCriterion extends APIlityCriterion {
    // website class attributes
    var $maxCpm;
    var $maxCpc;    
    var $url;

    // constructor
    function APIlityWebsiteCriterion(
      $url,
      $id,
      $belongsToAdGroupId,
      $criterionType,
      $isNegative,
      $isPaused,
      $maxCpm,
      $maxCpc,
      $status,
      $language,
      $destinationUrl
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityCriterion::APIlityCriterion(
          $id,
          $belongsToAdGroupId,
          $criterionType,
          $isNegative,
          $isPaused,
          $status,
          $language,
          $destinationUrl
      );
      // now construct the website criterion which inherits all other criterion
      // attributes
      $this->maxCpm = $maxCpm;
      $this->maxCpc = $maxCpc;      
      $this->url = $url;
    }

    // XML output
    function toXml() {
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      if ($this->getIsPaused()) {
        $isPaused = "true";
      }
      else {
        $isPaused = "false";
      }
      $xml = "<WebsiteCriterion>
  <url>" . $this->getUrl() . "</url>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <criterionType>" . $this->getCriterionType() . "</criterionType>
  <isNegative>" . $isNegative . "</isNegative>
  <isPaused>" . $isPaused . "</isPaused>
  <status>" . $this->getStatus() . "</status>
  <maxCpm>" . $this->getMaxCpm() . "</maxCpm>
  <maxCpc>" . $this->getMaxCpc() . "</maxCpc>  
  <language>" . $this->getLanguage() . "</language>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
</WebsiteCriterion>";
      return utf8_encode($xml);
    }

    // get functions
    function getMaxCpm() {
      return $this->maxCpm;
    }

    function getMaxCpc() {
      return $this->maxCpc;
    }

    function getUrl() {
      return $this->url;
    }

    function getCriterionData() {
      $criterionData = array(
                           'id' => $this->getId(),
                           'url' => $this->getUrl(),
                           'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                           'criterionType' => $this->getCriterionType(),
                           'isNegative' => $this->getIsNegative(),
                           'isPaused' => $this->getIsPaused(),
                           'maxCpm' => $this->getMaxCpm(),
                           'maxCpc' => $this->getMaxCpc(),                          
                           'status' => $this->getStatus(),
                           'language' => $this->getLanguage(),
                           'destinationUrl' => $this->getDestinationUrl()
                        );
      return $criterionData;
    }

    // set functions
    function setUrl($newUrl) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      // changing the url is not provided by the api so we need to emulate this
      // by removing and re-creating
      // then re-create the website with the new url set
      // make sure bool gets correctly transformed to string
      if ($this->getIsNegative()) {
        $isNegative = "true";
      }
      else {
        $isNegative = "false";
      }
      // danger! we need to think in micros so we need to transform the object
      // maxcpc to micros
      $soapParameters = "<addCriteria>
                           <criteria>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>Website</criterionType>
                             <negative>" . $isNegative . "</negative>
                             <maxCpm>" . 
                               $this->getMaxCpm() * EXCHANGE_RATE . "
                             </maxCpm>
                             <maxCpc>" . 
                               $this->getMaxCpc() * EXCHANGE_RATE . "
                             </maxCpc>
                             <language>" . $this->getLanguage() . "</language>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                             <url>" . $newUrl . "</url>
                           </criteria>
                         </addCriteria>";
      // add criterion to the google servers
      $someCriterion = $someSoapClient->call("addCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setUrl()", $soapParameters);
        return false;
      }
      // first delete current website
      $soapParameters = "<removeCriteria>
                           <adGroupId>" . 
                             $this->getBelongsToAdGroupId() . "
                           </adGroupId>
                           <criterionIds>" . $this->getId() . "</criterionIds>
                         </removeCriteria>";
      // talk to the google servers
      $someSoapClient->call("removeCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setUrl()", $soapParameters);
        return false;
      }
      // update local object
      $this->url = $newUrl;
      // changing the text of a keyword will change its id, so update object
      // id data
      $this->id = $someCriterion['addCriteriaReturn']['id'];
      return true;
    }

    function setMaxCpm($newMaxCpm) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      // danger! think in micros
      $soapParameters = "<updateCriteria>
                           <criteria>
                             <id>" . $this->getId() . "</id>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>Website</criterionType>
                             <maxCpm>" . $newMaxCpm * EXCHANGE_RATE . "</maxCpm>
                             <negative>" . $this->getIsNegative() . "</negative>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                           </criteria>
                         </updateCriteria>";
      // update the keyword on the google servers
      $someSoapClient->call("updateCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setMaxCpm()", $soapParameters);
        return false;
      }
      // update local object
      $this->maxCpm = $newMaxCpm;
      return true;
    }
    
    function setMaxCpc($newMaxCpc) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      // danger! think in micros
      $soapParameters = "<updateCriteria>
                           <criteria>
                             <id>" . $this->getId() . "</id>
                             <adGroupId>" . 
                               $this->getBelongsToAdGroupId() . "
                             </adGroupId>
                             <criterionType>Website</criterionType>
                             <maxCpc>" . $newMaxCpc * EXCHANGE_RATE . "</maxCpc>
                             <negative>" . $this->getIsNegative() . "</negative>
                             <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                           </criteria>
                         </updateCriteria>";
      // update the keyword on the google servers
      $someSoapClient->call("updateCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setMaxCpc()", $soapParameters);
        return false;
      }
      // update local object
      $this->maxCpc = $newMaxCpc;
      return true;
    }    
  }
?>
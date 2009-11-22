<?php

  // import this file only when we are not in OO mode
  // however, if we are in OO mode, the import happens in APIlityUser.php  
  if (!IS_ENABLED_OO_MODE) {
    require_once('Ad.inc.php');  
  }

  /*
   SUPER CLASS FOR ADS
  */
  class APIlityAd {
    // class attributes
    var $id;
    var $belongsToAdGroupId;
    var $status;
    var $isDisapproved;
    var $displayUrl;
    var $destinationUrl;
    var $adType;

    // constructor
    function APIlityAd (
        $id,
        $belongsToAdGroupId,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved,
        $adType
    ) {
      $this->id = $id;
      $this->belongsToAdGroupId = $belongsToAdGroupId;
      $this->displayUrl = $displayUrl;
      $this->destinationUrl = $destinationUrl;
      $this->status = $status;
      $this->isDisapproved = convertBool($isDisapproved);
      $this->adType = $adType;
    }

    // get functions
    function getId() {
      return $this->id;
    }

    function getBelongsToAdGroupId() {
      return $this->belongsToAdGroupId;
    }

    function getDestinationUrl() {
      return $this->destinationUrl ;
    }

    function getDisplayUrl() {
      return $this->displayUrl;
    }

    function getStatus() {
      return $this->status;
    }

    function getAdType() {
      return $this->adType;
    }

    function getIsDisapproved() {
       return $this->isDisapproved;
    }

    function getAdStats($startDate, $endDate) {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      $soapParameters = "<getAdStats>
                            <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                            <adIds>" . $this->getId() . "</adIds>
                            <startDay>" . $startDate . "</startDay>
                            <endDay>" . $endDate . "</endDay>
                         </getAdStats>";
      // query the google servers
      $adStats = $someSoapClient->call("getAdStats", $soapParameters);
      $soapClients->updateSoapRelatedData(
        extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getAdStats()", $soapParameters);
        return false;
      }
      $adStats['getAdStatsReturn']['cost'] = ((double) $adStats['getAdStatsReturn']['cost']) / EXCHANGE_RATE;
      return $adStats['getAdStatsReturn'];
    }
  }

  /*
    VIDEO ADS
  */
  class APIlityVideoAd extends APIlityAd {
    // class attributes
    var $image;
    var $name;
    var $video;

    // constructor
    function APIlityVideoAd (
        $id,
        $belongsToAdGroupId,
        $image,
        $name,
        $video,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityAd::APIlityAd(
          $id,
          $belongsToAdGroupId,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          'VideoAd'
      );
      // now construct the video ad which inherits all other ad attributes
      $this->image = $image;
      $this->video = $video;
      $this->name = $name;
    }

    function toXml() {
      if ($this->getIsDisapproved()) {
        $isDisapproved = "true";
      }
      else {
        $isDisapproved = "false";
      }
      $image = $this->getImage();
      $video = $this->getVideo();

      $xml = "<VideoAd>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <image>
    <type>" . $image['type'] . "</type>
    <name>" . xmlEscape($image['name']) . "</name>
    <width>" . $image['width'] . "</width>
    <height>" . $image['height'] . "</height>
    <imageUrl>" . $image['imageUrl'] . "</imageUrl>
    <thumbnailUrl>" . $image['thumbnailUrl'] . "</thumbnailUrl>
    <mimeType>" . $image['mimeType'] . "</mimeType>
  </image>
  <video>
    <duration>" . $video['duration'] . "</duration>
    <filename>" . xmlEscape($video['filename']) . "</filename>
    <preview>" . $video['preview'] . "</preview>
    <title>" . xmlEscape($video['title']) . "</title>
    <videoId>" . $video['videoId'] . "</videoId>
  </video>
  <name>" . xmlEscape($this->getName()) . "</name>
  <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
  <status>" . $this->getStatus() . "</status>
  <isDisapproved>" . $isDisapproved . "</isDisapproved>
</VideoAd>";
      return $xml;
    }
    // get functions
    function getName() {
      return $this->name;
    }

    function getImage() {
      return $this->image;
    }

    function getVideo() {
      return $this->video;
    }

    // report function
    function getAdData() {
      $adData = array(
                        'id' => $this->getId(),
                        'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                        'video' => $this->getVideo(),
                        'image' => $this->getImage(),
                        'name' => $this->getName(),
                        'displayUrl' => $this->getDisplayUrl(),
                        'destinationUrl' => $this->getDestinationUrl(),
                        'status' => $this->getStatus(),
                        'isDisapproved' => $this->getIsDisapproved()
                      );
      return $adData;
    }

    // set functions
    function setStatus ($newStatus) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>" . $newStatus . "</status>
                             <adType>VideoAd</adType>
                            </ads>
                         </updateAds>";
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setStatus()", $soapParameters);
        return false;
      }
      // update local object
      $this->status = $newStatus;
      return true;
    }
  }

  /*
    LOCAL BUSINESS ADS
  */
  class APIlityLocalBusinessAd extends APIlityAd {
    // class attributes
    var $address;
    var $businessImage;
    var $businessKey;
    var $businessName;
    var $fullBusinessName;
    var $city;
    var $countryCode;
    var $customIcon;
    var $customIconId;
    var $description1;
    var $description2;
    var $phoneNumber;
    var $postalCode;
    var $region;
    var $stockIcon;
    var $targetRadiusInKm;
    var $latitude;
    var $longitude;

    // constructor
    function APIlityLocalBusinessAd (
        $id,
        $belongsToAdGroupId,
        $address,
        $businessImage,
        $businessKey,
        $businessName,
        $fullBusinessName,
        $city,
        $countryCode,
        $customIcon,
        $customIconId,
        $description1,
        $description2,
        $phoneNumber,
        $postalCode,
        $region,
        $stockIcon,
        $targetRadiusInKm,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved,
        $latitude,
        $longitude
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityAd::APIlityAd(
          $id,
          $belongsToAdGroupId,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          'LocalBusinessAd'
      );
      // now construct the local business ad which inherits all other ad attributes
      $this->address = $address;
      $this->businessImage = $businessImage;
      $this->businessKey = $businessKey;
      $this->businessName = $businessName;
      $this->fullBusinessName = $fullBusinessName;      
      $this->city = $city;
      $this->countryCode = $countryCode;
      $this->customIcon = $customIcon;
      $this->customIconId = $customIconId;
      $this->description1 = $description1;
      $this->description2 = $description2;
      $this->phoneNumber = $phoneNumber;
      $this->postalCode = $postalCode;
      $this->region = $region;
      $this->stockIcon = $stockIcon;
      $this->targetRadiusInKm = $targetRadiusInKm;
      $this->latitude = $latitude;
      $this->longitude = $longitude;
    }

    // XML output
    function toXml() {
      if ($this->getIsDisapproved())
        $isDisapproved = "true";
      else
        $isDisapproved = "false";
      $businessImage = $this->getBusinessImage();
      $customIcon = $this->getCustomIcon();

      $xml = "<LocalBusinessAd>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <address>" . xmlEscape($this->getAddress()) . "</address>
  <businessImage>
    <image>
      <type>" . $businessImage['type'] . "</type>
      <name>" . xmlEscape($businessImage['name']) . "</name>
      <width>" . $businessImage['width'] . "</width>
      <height>" . $businessImage['height'] . "</height>
      <imageUrl>" . $businessImage['imageUrl'] . "</imageUrl>
      <thumbnailUrl>" . $businessImage['thumbnailUrl'] . "</thumbnailUrl>
      <mimeType>" . $businessImage['mimeType'] . "</mimeType>
    </image>
  </businessImage>
  <businessKey>" . $this->getBusinessKey() . "</businessKey>
  <businessName>" . xmlEscape($this->getBusinessName()) . "</businessName>
  <fullBusinessName>" . xmlEscape($this->getFullBusinessName()) . "</fullBusinessName>  
  <city>" . xmlEscape($this->getCity()) . "</city>
  <countryCode>" . $this->getCountryCode() . "</countryCode>
  <customIcon>
    <image>
      <type>" . $customIcon['type'] . "</type>
      <name>" . xmlEscape($customIcon['name']) . "</name>
      <width>" . $customIcon['width'] . "</width>
      <height>" . $customIcon['height'] . "</height>
      <imageUrl>" . $customIcon['imageUrl'] . "</imageUrl>
      <thumbnailUrl>" . $customIcon['thumbnailUrl'] . "</thumbnailUrl>
      <mimeType>" . $customIcon['mimeType'] . "</mimeType>
    </image>
  </customIcon>
  <customIconId>" . $this->getCustomIconId() . "</customIconId>
  <description1>" . xmlEscape($this->getDescription1()) . "</description1>
  <description2>" . xmlEscape($this->getDescription2()) . "</description2>
  <phoneNumber>" . $this->getPhoneNumber() . "</phoneNumber>
  <postalCode>" . $this->getPostalCode() . "</postalCode>
  <region>" . $this->getRegion() . "</region>
  <stockIcon>" . $this->getStockIcon() . "</stockIcon>
  <targetRadiusInKm>" . $this->getTargetRadiusInKm() . "</targetRadiusInKm>
  <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
  <status>" . $this->getStatus() . "</status>
  <isDisapproved>" . $isDisapproved . "</isDisapproved>
  <latitude>" . $this->getLatitude() . "</latitude>
  <longitude>" . $this->getLongitude() . "</longitude>
</LocalBusinessAd>";
      return $xml;
    }

    // get functions
    function getAddress() {
      return $this->address;
    }

    function getBusinessImage() {
      return $this->businessImage;
    }

    function getBusinessKey() {
      return $this->businessKey;
    }

    function getBusinessName() {
      return $this->businessName;
    }

    function getFullBusinessName() {
      return $this->fullBusinessName;
    }
    
    function getLatitude() {
      return $this->latitude;
    }
    
    function getLongitude() {
      return $this->longitude;
    }
    
    function getCity() {
      return $this->city;
    }

    function getCountryCode() {
      return $this->countryCode;
    }

    function getCustomIcon() {
      return $this->customIcon;
    }

    function getCustomIconId() {
      return $this->customIconId;
    }

    function getDescription1() {
      return $this->description1;
    }

    function getDescription2() {
      return $this->description2;
    }

    function getPhoneNumber() {
      return $this->phoneNumber;
    }

    function getPostalCode() {
      return $this->postalCode;
    }

    function getRegion() {
      return $this->region;
    }

    function getStockIcon() {
      return $this->stockIcon;
    }

    function getTargetRadiusInKm() {
      return $this->targetRadiusInKm;
    }

    // report function
    function getAdData() {
      $adData = array(
                        'id' => $this->getId(),
                        'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                        'address' => $this->getAddress(),
                        'businessImage' => $this->getBusinessImage(),
                        'businessKey' => $this->getBusinessKey(),
                        'businessName' => $this->getBusinessName(),
                        'fullBusinessName' => $this->getFullBusinessName(),
                        'city' => $this->getCity(),
                        'countryCode' => $this->getCountryCode(),
                        'customIcon' => $this->getCustomIcon(),
                        'customIconId' => $this->getCustomIconId(),
                        'description1' => $this->getDescription1(),
                        'description2' => $this->getDescription2(),
                        'phoneNumber' => $this->getPhoneNumber(),
                        'postalCode' => $this->getPostalCode(),
                        'region' => $this->getRegion(),
                        'stockIcon' => $this->getStockIcon(),
                        'targetRadiusInKm' => $this->getTargetRadiusInKm(),
                        'displayUrl' => $this->getDisplayUrl(),
                        'destinationUrl' => $this->getDestinationUrl(),
                        'status' => $this->getStatus(),
                        'isDisapproved' => $this->getIsDisapproved(),
                        'latitude' => $this->getLatitude(),
                        'longitude' => $this->getLongitude()
                      );
      return $adData;
    }

    // set functions
    function setStatus ($newStatus) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>" . $newStatus . "</status>
                             <adType>LocalBusinessAd</adType>
                            </ads>
                         </updateAds>";
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setStatus()", $soapParameters);
        return false;
      }
      // update local object
      $this->status = $newStatus;
      return true;
    }
  }

  /*
    COMMERCE ADS
  */
  class APIlityCommerceAd extends APIlityAd {
    // class attributes
    var $description1;
    var $description2;
    var $headline;
    var $postPriceAnnotation;
    var $prePriceAnnotation;
    var $priceString;
    var $productImage = array();

    // constructor
    function APIlityCommerceAd (
        $id,
        $belongsToAdGroupId,
        $description1,
        $description2,
        $headline,
        $postPriceAnnotation,
        $prePriceAnnotation,
        $priceString,
        $productImage,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityAd::APIlityAd(
          $id,
          $belongsToAdGroupId,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          'CommerceAd'
      );
      // now construct the commerce ad which inherits all other ad attributes
      $this->description1 = $description1;
      $this->description2 = $description2;
      $this->headline = $headline;
      $this->postPriceAnnotation = $postPriceAnnotation;
      $this->prePriceAnnotation = $prePriceAnnotation;
      $this->priceString = $priceString;
      $this->productImage = $productImage;
    }

    // XML output
    function toXml() {
      if ($this->getIsDisapproved())
        $isDisapproved = "true";
      else
        $isDisapproved = "false";
      $productImage = $this->getProductImage();

      $xml = "<CommerceAd>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <description1>" . xmlEscape($this->getDescription1()) . "</description1>
  <description2>" . xmlEscape($this->getDescription2()) . "</description2>
  <headline>" . xmlEscape($this->getHeadline()) . "</headline>
  <postPriceAnnotation>" . $this->getPostPriceAnnotation() . "</postPriceAnnotation>
  <prePriceAnnotation>" . $this->getPrePriceAnnotation() . "</prePriceAnnotation>
  <priceString>" . $this->getPriceString() . "</priceString>
  <productImage>
    <image>
      <type>" . $productImage['type'] . "</type>
      <name>" . xmlEscape($productImage['name']) . "</name>
      <width>" . $productImage['width'] . "</width>
      <height>" . $productImage['height'] . "</height>
      <imageUrl>" . $productImage['imageUrl'] . "</imageUrl>
      <thumbnailUrl>" . $productImage['thumbnailUrl'] . "</thumbnailUrl>
      <mimeType>" . $productImage['mimeType'] . "</mimeType>
    </image>
  </productImage>
  <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
  <status>" . $this->getStatus() . "</status>
  <isDisapproved>" . $isDisapproved . "</isDisapproved>
</CommerceAd>";
      return $xml;
    }

    // get functions
    function getDescription1() {
      return $this->description1;
    }

    function getDescription2() {
      return $this->description2;
    }

    function getHeadline() {
      return $this->headline;
    }

    function getPostPriceAnnotation() {
      return $this->postPriceAnnotation;
    }

    function getPrePriceAnnotation() {
      return $this->prePriceAnnotation;
    }

    function getPriceString() {
      return $this->priceString;
    }

    function getProductImage() {
      return $this->productImage;
    }

    // report function
    function getAdData() {
      $adData = array(
                        'id' => $this->getId(),
                        'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                        'description1' => $this->getDescription1(),
                        'description2' => $this->getDescription2(),
                        'headline' => $this->getHeadline(),
                        'postPriceAnnotation' => $this->getPostPriceAnnotation(),
                        'prePriceAnnotation' => $this->getPrePriceAnnotation(),
                        'priceString' => $this->getPriceString(),
                        'productImage' => $this->getProductImage(),
                        'displayUrl' => $this->getDisplayUrl(),
                        'destinationUrl' => $this->getDestinationUrl(),
                        'status' => $this->getStatus(),
                        'isDisapproved' => $this->getIsDisapproved()
                      );
      return $adData;
    }

    // set functions
    function setStatus ($newStatus) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>" . $newStatus . "</status>
                             <adType>CommerceAd</adType>
                            </ads>
                         </updateAds>";
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setStatus()", $soapParameters);
        return false;
      }
      // update local object
      $this->status = $newStatus;
      return true;
    }
  }

  /*
    MOBILE ADS
  */
  class APIlityMobileAd extends APIlityAd {
    // class attributes
    var $businessName;
    var $countryCode;
    var $description;
    var $headline;
    var $markupLanguages = array();
    var $mobileCarriers = array();
    var $phoneNumber;

    // constructor
    function APIlityMobileAd (
        $id,
        $belongsToAdGroupId,
        $businessName,
        $countryCode,
        $description,
        $headline,
        $markupLanguages,
        $mobileCarriers,
        $phoneNumber,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityAd::APIlityAd(
          $id,
          $belongsToAdGroupId,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          'MobileAd'
      );
      // now construct the mobile ad which inherits all other ad attributes
      $this->businessName = $businessName;
      $this->countryCode = $countryCode;
      $this->description = $description;
      $this->headline = $headline;
      $this->markupLanguages = convertToArray($markupLanguages);
      $this->mobileCarriers = convertToArray($mobileCarriers);
      $this->phoneNumber = $phoneNumber;
    }

    // XML output
    function toXml() {
      if ($this->getIsDisapproved())
        $isDisapproved = "true"; else $isDisapproved = "false";
      $markupLanguages = $this->getMarkupLanguages();
      $markupLanguagesXml = "";
      foreach($markupLanguages as $markupLanguage) {
        $markupLanguagesXml .= "<markupLanguage>" . $markupLanguage . "</markupLanguage>";
      }

      $mobileCarriers = $this->getMobileCarriers();
      $mobileCarriersXml = "";
      foreach($mobileCarriers as $mobileCarrier) {
        $mobileCarriersXml .= "<mobileCarrier>" . $mobileCarrier . "</mobileCarrier>";
      }

      $xml = "<MobileAd>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <businessName>" . xmlEscape($this->getBusinessName()) . "</businessName>
  <countryCode>" . $this->getCountryCode() . "</countryCode>
  <description>" . xmlEscape($this->getDescription()) . "</description>
  <headline>" . xmlEscape($this->getHeadline()) . "</headline>
  <markupLanguages>" . $markupLanguagesXml . "</markupLanguages>
  <mobileCarriers>" . $mobileCarriersXml . "</mobileCarriers>
  <phoneNumber>" . $this->getPhoneNumber() . "</phoneNumber>
  <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
  <status>" . $this->getStatus() . "</status>
  <isDisapproved>" . $isDisapproved . "</isDisapproved>
</MobileAd>";
      return $xml;
    }

    // get functions
    function getBusinessName() {
      return $this->businessName;
    }

    function getCountryCode() {
      return $this->countryCode;
    }

    function getDescription() {
      return $this->description;
    }

    function getHeadline() {
      return $this->headline;
    }

    function getMarkupLanguages() {
      return $this->markupLanguages;
    }

    function getMobileCarriers() {
      return $this->mobileCarriers;
    }

    function getPhoneNumber() {
      return $this->phoneNumber;
    }
    // report function
    function getAdData() {
      $adData = array(
                        'id' => $this->getId(),
                        'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                        'businessName' => $this->getBusinessName(),
                        'countryCode' => $this->getCountryCode(),
                        'description' => $this->getDescription(),
                        'headline' => $this->getHeadline(),
                        'markupLanguages' => $this->getMarkupLanguages(),
                        'mobileCarriers' => $this->getMobileCarriers(),
                        'phoneNumber' => $this->getPhoneNumber(),
                        'displayUrl' => $this->getDisplayUrl(),
                        'destinationUrl' => $this->getDestinationUrl(),
                        'status' => $this->getStatus(),
                        'isDisapproved' => $this->getIsDisapproved()
                      );
      return $adData;
    }

    // set functions
    function setStatus ($newStatus) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>" . $newStatus . "</status>
                             <adType>MobileAd</adType>
                            </ads>
                         </updateAds>";
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setStatus()", $soapParameters);
        return false;
      }
      // update local object
      $this->status = $newStatus;
      return true;
    }
  }

  /*
    MOBILE IMAGE ADS
  */
  class APIlityMobileImageAd extends APIlityAd {
    // class attributes
    var $image;
    var $markupLanguages = array();
    var $mobileCarriers = array();

    // constructor
    function APIlityMobileImageAd (
        $id,
        $belongsToAdGroupId,
        $image,
        $markupLanguages,
        $mobileCarriers,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityAd::APIlityAd(
          $id,
          $belongsToAdGroupId,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          'MobileImageAd'
      );
      // now construct the mobile ad which inherits all other ad attributes
      $this->markupLanguages = convertToArray($markupLanguages);
      $this->mobileCarriers = convertToArray($mobileCarriers);
      $this->image = $image;
    }

    // XML output
    function toXml() {
      if ($this->getIsDisapproved())
        $isDisapproved = "true"; else $isDisapproved = "false";
      $markupLanguages = $this->getMarkupLanguages();
      $markupLanguagesXml = "";
      foreach($markupLanguages as $markupLanguage) {
        $markupLanguagesXml .= "<markupLanguage>" . $markupLanguage . "</markupLanguage>";
      }

      $mobileCarriers = $this->getMobileCarriers();
      $mobileCarriersXml = "";
      foreach($mobileCarriers as $mobileCarrier) {
        $mobileCarriersXml .= "<mobileCarrier>" . $mobileCarrier . "</mobileCarrier>";
      }
      $image = $this->getImage();

      $xml = "<MobileImageAd>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <image>
    <type>" . $image['type'] . "</type>
    <name>" . xmlEscape($image['name']) . "</name>
    <width>" . $image['width'] . "</width>
    <height>" . $image['height'] . "</height>
    <imageUrl>" . $image['imageUrl'] . "</imageUrl>
    <thumbnailUrl>" . $image['thumbnailUrl'] . "</thumbnailUrl>
    <shrunkenUrl>" . $image['shrunkenUrl'] . "</shrunkenUrl>
    <mimeType>" . $image['mimeType'] . "</mimeType>
  </image>
  <markupLanguages>" . $markupLanguagesXml . "</markupLanguages>
  <mobileCarriers>" . $mobileCarriersXml . "</mobileCarriers>
  <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
  <status>" . $this->getStatus() . "</status>
  <isDisapproved>" . $isDisapproved . "</isDisapproved>
</MobileImageAd>";
      return $xml;
    }

    // get functions
    function getImage() {
      return $this->image;
    }

    function getMarkupLanguages() {
      return $this->markupLanguages;
    }

    function getMobileCarriers() {
      return $this->mobileCarriers;
    }

    // report function
    function getAdData() {
      $adData = array(
                        'id' => $this->getId(),
                        'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                        'image' => $this->getImage(),
                        'markupLanguages' => $this->getMarkupLanguages(),
                        'mobileCarriers' => $this->getMobileCarriers(),
                        'displayUrl' => $this->getDisplayUrl(),
                        'destinationUrl' => $this->getDestinationUrl(),
                        'status' => $this->getStatus(),
                        'isDisapproved' => $this->getIsDisapproved()
                      );
      return $adData;
    }

    // set functions
    function setStatus ($newStatus) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>" . $newStatus . "</status>
                             <adType>MobileImageAd</adType>
                            </ads>
                         </updateAds>";
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setStatus()", $soapParameters);
        return false;
      }
      // update local object
      $this->status = $newStatus;
      return true;
    }
  }

  /*
   TEXT ADS
  */

  class APIlityTextAd extends APIlityAd {
    // class attributes
    var $headline;
    var $description1;
    var $description2;

    // constructor
    function APIlityTextAd (
        $id,
        $belongsToAdGroupId,
        $headline,
        $description1,
        $description2,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityAd::APIlityAd(
          $id,
          $belongsToAdGroupId,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          'TextAd'
      );
      // now construct the text ad which inherits all other ad attributes
      $this->headline = $headline;
      $this->description1 = $description1;
      $this->description2 = $description2;
    }

    // XML output
    function toXml() {
      if ($this->getIsDisapproved())
        $isDisapproved = "true";
      else
        $isDisapproved = "false";
      $xml = "<TextAd>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <headline>" . xmlEscape($this->getHeadline()) . "</headline>
  <description1>" . xmlEscape($this->getDescription1()) . "</description1>
  <description2>" . xmlEscape($this->getDescription2()) . "</description2>
  <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
  <status>" . $this->getStatus() . "</status>
  <isDisapproved>" . $isDisapproved . "</isDisapproved>
</TextAd>";
      return $xml;
    }

    // get functions
    function getHeadline() {
      return $this->headline;
    }

    function getDescription1() {
      return $this->description1;
    }

    function getDescription2() {
      return $this->description2;
    }

    // report function
    function getAdData() {
      $adData = array(
                        'id' => $this->getId(),
                        'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                        'headline' => $this->getHeadline(),
                        'description1' => $this->getDescription1(),
                        'description2' => $this->getDescription2(),
                        'displayUrl' => $this->getDisplayUrl(),
                        'destinationUrl' => $this->getDestinationUrl(),
                        'status' => $this->getStatus(),
                        'isDisapproved' => $this->getIsDisapproved()
                      );
      return $adData;
    }

    // set functions
    function setStatus ($newStatus) {
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>" . $newStatus . "</status>
                             <adType>TextAd</adType>
                            </ads>
                         </updateAds>";
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setStatus()", $soapParameters);
        return false;
      }
      // update local object
      $this->status = $newStatus;
      return true;
    }

    function setHeadline ($newHeadline) {
      // setting the headline is not provided by the api so emulating this by
      // deleting and then re-creating the ad
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      // then recreate it with the new headline set
      $soapParameters = "<addAds>
                            <ads>
                              <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                              <headline>" . $newHeadline . "</headline>
                              <description1>" . $this->getDescription1() . "</description1>
                              <description2>" . $this->getDescription2() . "</description2>
                              <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
                              <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                              <status>" . $this->getStatus() . "</status>
                              <adType>TextAd</adType>
                            </ads>
                          </addAds>";
      // add the ad to the google servers
      $someAd = $someSoapClient->call("addAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setHeadline()", $soapParameters);
        return false;
      }
      // first delete the current ad
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>Disabled</status>
                             <adType>TextAd</adType>
                            </ads>
                         </updateAds>";
      // delete the ad on the google servers
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setHeadline()", $soapParameters);
        return false;
      }
      // update local object
      $this->headline = $someAd['addAdsReturn']['headline'];
      // changing the headline of an ad will change its id so update local object
      $this->id = $someAd['addAdsReturn']['id'];
      return true;
    }

    function setDescription1 ($newDescription1) {
      // update the google servers
      // setting the description1 is not provided by the api so emulating this
      // by deleting and then re-creating the ad
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      // then recreate it with the new description1 set
      $soapParameters = "<addAds>
                            <ads>
                              <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                              <headline>" . $this->getHeadline() . "</headline>
                              <description1>" . $newDescription1 . "</description1>
                              <description2>" . $this->getDescription2() . "</description2>
                              <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
                              <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                              <status>" . $this->getStatus() . "</status>
                              <adType>TextAd</adType>
                            </ads>
                          </addAds>";
      // add the ad to the google servers
      $someAd = $someSoapClient->call("addAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDescription1()", $soapParameters);
        return false;
      }
      // first delete the current ad
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>Disabled</status>
                             <adType>TextAd</adType>
                            </ads>
                         </updateAds>";
      // delete the ad on the google servers
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDescription1()", $soapParameters);
        return false;
      }
      // update local object
      $this->description1 = $someAd['addAdsReturn']['description1'];
      // changing the description1 of an ad will change its id so update local object
      $this->id = $someAd['addAdsReturn']['id'];
      return true;
    }

    function setDescription2 ($newDescription2) {
      // setting the description2 is not provided by the api so emulating this
      // by deleting and then re-creating the ad
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      // then recreate it with the new description2 set
      $soapParameters = "<addAds>
                            <ads>
                              <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                              <headline>" . $this->getHeadline() . "</headline>
                              <description1>" . $this->getDescription1() . "</description1>
                              <description2>" . $newDescription2 . "</description2>
                              <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
                              <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                              <status>" . $this->getStatus() . "</status>
                              <adType>TextAd</adType>
                            </ads>
                          </addAds>";
      // add the ad to the google servers
      $someAd = $someSoapClient->call("addAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDescription2()", $soapParameters);
        return false;
      }
      // first delete the current ad
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>Disabled</status>
                             <adType>TextAd</adType>
                            </ads>
                         </updateAds>";
      // delete the ad on the google servers
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDescription2()", $soapParameters);
        return false;
      }
      // update local object
      $this->description2 = $someAd['addAdsReturn']['description2'];
      // changing the description2 of an ad will change its id so update local object
      $this->id = $someAd['addAdsReturn']['id'];
      return true;
    }

    function setDisplayUrl ($newDisplayUrl) {
      // setting the display url is not provided by the api so emulating this
      // by deleting and then re-creating the ad
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      // then recreate it with the new display url set
      $soapParameters = "<addAds>
                            <ads>
                              <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                              <headline>" . $this->getHeadline() . "</headline>
                              <description1>" . $this->getDescription1() . "</description1>
                              <description2>" . $this->getDescription2() . "</description2>
                              <displayUrl>" . $newDisplayUrl . "</displayUrl>
                              <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
                              <status>" . $this->getStatus() . "</status>
                              <adType>TextAd</adType>
                            </ads>
                          </addAds>";
      // add the ad to the google servers
      $someAd = $someSoapClient->call("addAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDisplayUrl()", $soapParameters);
        return false;
      }
      // first delete the current ad
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>Disabled</status>
                             <adType>TextAd</adType>
                            </ads>
                         </updateAds>";
      // delete the ad on the google servers
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDisplayUrl()", $soapParameters);
        return false;
      }
      // update local object
      $this->displayUrl = $someAd['addAdsReturn']['displayUrl'];
      // changing the display url of an ad will change its id so update local object
      $this->id = $someAd['addAdsReturn']['id'];
      return true;
    }

    function setDestinationUrl ($newDestinationUrl) {
      // setting the destination url is not provided by the api so emulating
      // this by deleting and then re-creating the ad
      // update the google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdClient();
      // then recreate it with the new destination url set
      $soapParameters = "<addAds>
                            <ads>
                              <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                              <headline>" . $this->getHeadline() . "</headline>
                              <description1>" . $this->getDescription1() . "</description1>
                              <description2>" . $this->getDescription2() . "</description2>
                              <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
                              <destinationUrl>" . $newDestinationUrl . "</destinationUrl>
                              <status>" . $this->getStatus() . "</status>
                              <adType>TextAd</adType>
                            </ads>
                          </addAds>";
      // add the ad to the google servers
      $someAd = $someSoapClient->call("addAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDestinationUrl()", $soapParameters);
        return false;
      }
      // first delete the current ad
      $soapParameters = "<updateAds>
                           <ads>
                             <adGroupId>" . $this->getBelongsToAdGroupId() . "</adGroupId>
                             <id>" . $this->getId() . "</id>
                             <status>Disabled</status>
                             <adType>TextAd</adType>
                            </ads>
                         </updateAds>";
      // delete the ad on the google servers
      $someSoapClient->call("updateAds", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setDestinationUrl()", $soapParameters);
        return false;
      }
      // update local object
      $this->destinationUrl = $someAd['addAdsReturn']['destinationUrl'];
      // changing the destination url of an ad will change its id so update local object
      $this->id = $someAd['addAdsReturn']['id'];
      return true;
    }
  }

  /*
   IMAGE ADS
  */

  class APIlityImageAd extends APIlityAd {
    // class attributes
     var $image = array();

    // constructor
    function APIlityImageAd (
        $id,
        $belongsToAdGroupId,
        $image,
        $displayUrl,
        $destinationUrl,
        $status,
        $isDisapproved
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityAd::APIlityAd(
          $id,
          $belongsToAdGroupId,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          'ImageAd'
      );

      // now construct the image ad which inherits all other ad attributes
      $this->image = $image;
    }

    // XML output
    function toXml() {
      if ($this->getIsDisapproved())
        $isDisapproved = "true";
      else
        $isDisapproved = "false";
      $image = $this->getImage();
      $xml = "<ImageAd>
  <id>" . $this->getId() . "</id>
  <belongsToAdGroupId>" . $this->getBelongsToAdGroupId() . "</belongsToAdGroupId>
  <image>
    <type>" . $image['type'] . "</type>
    <name>" . xmlEscape($image['name']) . "</name>
    <width>" . $image['width'] . "</width>
    <height>" . $image['height'] . "</height>
    <imageUrl>" . $image['imageUrl'] . "</imageUrl>
    <thumbnailUrl>" . $image['thumbnailUrl'] . "</thumbnailUrl>
    <shrunkenUrl>" . @$image['shrunkenUrl'] . "</shrunkenUrl>
    <mimeType>" . $image['mimeType'] . "</mimeType>
  </image>
  <displayUrl>" . $this->getDisplayUrl() . "</displayUrl>
  <destinationUrl>" . $this->getDestinationUrl() . "</destinationUrl>
  <status>" . $this->getStatus() . "</status>
  <isDisapproved>" . $isDisapproved . "</isDisapproved>
</ImageAd>";
      return $xml;
    }

    // get functions
    function getImage() {
      return $this->image;
    }

    // report function
    function getAdData() {
      $adData = array(
                        'id' => $this->getId(),
                        'belongsToAdGroupId' => $this->getBelongsToAdGroupId(),
                        'image' => $this->getImage(),
                        'displayUrl' => $this->getDisplayUrl(),
                        'destinationUrl' => $this->getDestinationUrl(),
                        'status' => $this->getStatus(),
                        'isDisapproved' => $this->getIsDisapproved()
                      );
      return $adData;
    }
    // set functions
    // none, as these functions would require the base64 data for uploading the
    // image ad again after  deleting it (emulating changes by first deleting
    // things and then recreating them)
  }  
?>
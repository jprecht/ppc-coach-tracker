<?php
  function getXmlReport(
      $name,
      $selectedReportType,
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false 
  ) {
    // client email xml parameters
    $clientEmailsXml = '';
    if (sizeOf($clientEmails) > 0) {
      // we are expecting client emails like this:
      // array("someone@somewhere.xyz", "anyone@anywhere.xyz")
      foreach($clientEmails as $clientEmail) {
        $clientEmailsXml .=
            "<clientEmails>" . trim($clientEmail) . "</clientEmails>";
      }
    }

    // aggregation types
    $aggregationTypesXml = '';
    if (sizeOf($aggregationTypes > 0)) {
      foreach ($aggregationTypes as $aggregationType) {
        $aggregationTypesXml .=
            "<aggregationTypes>" . $aggregationType . "</aggregationTypes>";  
      }
    }

    // campaign ids
    $campaignsXml = '';
    if (sizeOf($campaigns > 0)) {
      foreach ($campaigns as $campaign) {
        $campaignsXml .= "<campaigns>" . $campaign . "</campaigns>";  
      }
    }

    // campaign statuses
    $campaignStatusesXml = '';
    if (sizeOf($campaignStatuses > 0)) {
      foreach ($campaignStatuses as $campaignStatus) {
        $campaignStatusesXml .=
            "<campaignStatuses>" . $campaignStatus . "</campaignStatuses>";  
      }
    }

    // adgroup ids
    $adGroupsXml = '';
    if (sizeOf($adGroups > 0)) {
      foreach ($adGroups as $adGroup) {
        $adGroupsXml .= "<adGroups>" . $adGroup . "</adGroups>";  
      }
    }

    // adgroup statuses
    $adGroupStatusesXml = '';
    if (sizeOf($adGroupStatuses > 0)) {
      foreach ($adGroupStatuses as $adGroupStatus) {
        $adGroupStatusesXml .=
            "<adGroupStatuses>" . $adGroupStatus . "</adGroupStatuses>";  
      }
    }

    // keyword ids
    $keywordsXml = '';
    if (sizeOf($keywords > 0)) {
      foreach ($keywords as $keyword) {
        $keywordsXml .= "<keywords>" . $keyword . "</keywords>";  
      }
    }

    // keyword statuses
    $keywordStatusesXml = '';
    if (sizeOf($keywordStatuses > 0)) {
      foreach ($keywordStatuses as $keywordStatus) {
        $keywordStatusesXml .=
            "<keywordStatuses>" . $keywordStatus . "</keywordStatuses>";  
      }
    }    

    // cross client or not
    if ($isCrossClient) $isCrossClient = "true"; else $isCrossClient = "false";

    // compile first parts of xml
    $reportXml = "<name>" . $name . "</name>
                  <selectedReportType>" . $selectedReportType . "</selectedReportType>" .
                  $aggregationTypesXml . "
                  <startDay>" . $startDay . "</startDay>
                  <endDay>" . $endDay . "</endDay>" .
                  $campaignsXml . 
                  $campaignStatusesXml . 
                  $adGroupsXml . 
                  $adGroupStatusesXml . 
                  $keywordsXml . 
                  $keywordStatusesXml . "
                  <crossClient>" . $isCrossClient . "</crossClient>" . 
                  $clientEmailsXml;

    // selected columns
    $selectedColumnsXml = '';
    if (sizeOf($selectedColumns) > 0) {
      foreach($selectedColumns as $selectedColumn) {
        $selectedColumnsXml .=
            "<selectedColumns>" . trim($selectedColumn) . "</selectedColumns>";
      }
    }
    $reportXml .=  $selectedColumnsXml;
    
    // keyword type
    if ($keywordType) $reportXml .=
      "<keywordType>" . $keywordType . "</keywordType>";
      
    // adwords type  
    if ($adWordsType) $reportXml .=
      "<adWordsType>" . $adWordsType . "</adWordsType>";
      
    // include zero impression  
    if ($includeZeroImpression) {
      $includeZeroImpression = "true";
    }
    else {
      $includeZeroImpression = "false";
    }
    $reportXml .=
        "<includeZeroImpression>" . $includeZeroImpression . "</includeZeroImpression>";     
    
    // finalize xml
    $reportXml = "<job xsi:type='DefinedReportJob'>" . 
                    $reportXml . "
                  </job>";                       
    return scheduleReportJob(
        $reportXml,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl);
  }
  
  function getTsvReport(
      $name,
      $selectedReportType,
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false 
) {
    return xml2Tsv(
      getXmlReport(
          $name,
          $selectedReportType,
          $startDay,
          $endDay,
          $selectedColumns,
          $aggregationTypes,
          $campaigns,
          $campaignStatuses,
          $adGroups,
          $adGroupStatuses,
          $keywords,
          $keywordStatuses,
          $adWordsType,
          $keywordType,
          $isCrossClient,
          $clientEmails,
          $includeZeroImpression,
          $sleepTime,
          $validateFirst,
          $onlyReturnDownloadUrl
      ),
      $onlyReturnDownloadUrl
    );
  }
  
  function getKeywordXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Keyword',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getKeywordTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Keyword',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getSearchQueryXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Query',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getSearchQueryTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Query',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getGeographicXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Geographic',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getGeographicTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Geographic',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getAccountStructureXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Structure',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getAccountStructureTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Structure',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getContentPlacementXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'ContentPlacement',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getContentPlacementTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'ContentPlacement',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getCreativeXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Creative',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getCreativeTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Creative',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }

  function getReachAndFrequencyXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'ReachAndFrequency',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }

  function getReachAndFrequencyTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'ReachAndFrequency',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }

  function getUrlXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Url',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }

  function getUrlTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Url',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }

  function getCampaignXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Campaign',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }

  function getCampaignTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Campaign',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getAdGroupXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'AdGroup',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getAdGroupTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'AdGroup',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }
  
  function getAccountXmlReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getXmlReport(
        $name,
        'Account',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl
    );
  }

  function getAccountTsvReport(
      $name, 
      $startDay,
      $endDay,
      $selectedColumns,
      $aggregationTypes,
      $campaigns = array(),
      $campaignStatuses = array(),
      $adGroups = array(),
      $adGroupStatuses = array(),
      $keywords = array(),
      $keywordStatuses = array(),
      $adWordsType = '',
      $keywordType = '',
      $isCrossClient = false,
      $clientEmails = array(),
      $includeZeroImpression = false,
      $sleepTime = 30,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false   
  ) {
    return getTsvReport(
        $name,
        'Account',
        $startDay,
        $endDay,
        $selectedColumns,
        $aggregationTypes,
        $campaigns,
        $campaignStatuses,
        $adGroups,
        $adGroupStatuses,
        $keywords,
        $keywordStatuses,
        $adWordsType,
        $keywordType,
        $isCrossClient,
        $clientEmails,
        $includeZeroImpression,
        $sleepTime,
        $validateFirst,
        $onlyReturnDownloadUrl 
    );
  }  

  function scheduleReportJob(
      $reportXml,
      $sleepTime,
      $validateFirst = false,
      $onlyReturnDownloadUrl = false
  ) {
    if ($validateFirst) {
      if (!validateReportJob($reportXml)) return false;
    }        
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getReportClient();
    $soapParameters = "<scheduleReportJob xmlns='" . REPORT_WSDL_URL . "'>" .     
                         $reportXml  . "
                       </scheduleReportJob>";                       
    // talk to the google servers and schedule report
    $someSchedule = $someSoapClient->call("scheduleReportJob", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":scheduleReportJob()", $soapParameters);
      return false;
    }
    $soapParameters = "<getReportJobStatus xmlns='" . REPORT_WSDL_URL . "'>
                         <reportJobId>" . $someSchedule['scheduleReportJobReturn'] . "</reportJobId>
                       </getReportJobStatus>";
    // check the status of the scheduled report
    $reportStatus = $someSoapClient->call("getReportJobStatus", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":scheduleReportJob()", $soapParameters);
      return false;
    }
    // busy waiting till report is finished (or till creation fails)
    while ( (strcmp($reportStatus['getReportJobStatusReturn'], "InProgress") == 0) ||
            (strcmp($reportStatus['getReportJobStatusReturn'], "Pending") == 0)
    ) {
      $reportStatus = $someSoapClient->call("getReportJobStatus", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":scheduleReportJob()", $soapParameters);
        return false;
      }      
      // report failed :(
      if  (strcmp($reportStatus['getReportJobStatusReturn'], "Failed") == 0) {
        if (!SILENCE_STEALTH_MODE) {
          trigger_error("<b>APIlity PHP library => Warning:</b> Sorry, but for some mysterious reason I could not finish your report request", E_USER_WARNING);
        }
        return false;
      }
      // report succeeded :)      
      if (strcmp($reportStatus['getReportJobStatusReturn'], "Completed") == 0) {        
        return downloadXmlReport(
            $someSchedule['scheduleReportJobReturn'],
            $onlyReturnDownloadUrl);
      }
      // busy waiting with n seconds break
      sleep($sleepTime);
    }
  }

  function downloadTsvReport($reportId, $onlyReturnDownloadUrl = false) {
    return xml2Tsv(downloadXmlReport($reportId, $onlyReturnDownloadUrl = false));
  }

  function downloadXmlReport($reportId, $onlyReturnDownloadUrl = false) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getReportClient();
    $zipOrNot = '';
    if (!USE_SANDBOX) $zipOrNot = 'Gzip';
    $soapParameters  = "<get" . $zipOrNot . "ReportDownloadUrl>
                          <reportJobId>" . $reportId . "</reportJobId>
                        </get" . $zipOrNot . "ReportDownloadUrl>";
    $reportUrl = $someSoapClient->call("get" . $zipOrNot . "ReportDownloadUrl", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":downloadXmlReport()", $soapParameters);
      return false;
    }
    if (isset($onlyReturnDownloadUrl) && $onlyReturnDownloadUrl) {
      return $reportUrl['get'.$zipOrNot.'ReportDownloadUrlReturn'];
    }
    // open connection to the Google server via cURL
    $curlConnection = curl_init();
    curl_setopt($curlConnection, CURLOPT_URL, $reportUrl['get'.$zipOrNot.'ReportDownloadUrlReturn']);
    curl_setopt($curlConnection, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlConnection, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curlConnection, CURLOPT_RETURNTRANSFER, TRUE);     
    $reportData = curl_exec($curlConnection); 
    // inflate the gzipped report we got
    if (!USE_SANDBOX) {
      $reportXml = gzinflate(substr($reportData, 10));
    }
    else {
      $reportXml = $reportData; 
    }
    if (curl_errno($curlConnection)) {
      if (!SILENCE_STEALTH_MODE) {
        trigger_error("<b>APIlity PHP library => Warning:</b> Sorry, there was a problem while downloading your report. The cURL error message is: " . curl_error($curlConnection), E_USER_WARNING);
      }
      curl_close($curlConnection);
      return false;
    }
    curl_close($curlConnection);
     
    // PHP version is >= 5, i.e. only DOM is avalable
    if (version_compare(phpversion(), "5.0.0", ">=")) {
      $xmlDomDocument = new DOMDocument();
      $xmlDomDocument->loadXML($reportXml);
      $singleRows = $xmlDomDocument->getElementsByTagName("row");

      foreach($singleRows as $row) {        
        if ($row->getAttribute("budgetAmount")) $row->setAttribute("budgetAmount", ((String) ((double) $row->getAttribute("budgetAmount")) / EXCHANGE_RATE));
        if ($row->getAttribute("cpmHeads")) $row->setAttribute("cpmHeads", ((String) ((double) $row->getAttribute("cpmHeads")) / EXCHANGE_RATE));
        if ($row->getAttribute("costPerHead")) $row->setAttribute("costPerHead", ((String) ((double) $row->getAttribute("costPerHead")) / EXCHANGE_RATE));
        if ($row->getAttribute("estimatedVideoCpm")) $row->setAttribute("estimatedVideoCpm", ((String) ((double) $row->getAttribute("estimatedVideoCpm")) / EXCHANGE_RATE));
        if ($row->getAttribute("cpt")) $row->setAttribute("cpt", ((String) ((double) $row->getAttribute("cpt")) / EXCHANGE_RATE));
        if ($row->getAttribute("costPerConv")) $row->setAttribute("costPerConv", ((String) ((double) $row->getAttribute("costPerConv")) / EXCHANGE_RATE));
        if ($row->getAttribute("convCost")) $row->setAttribute("convCost", ((String) ((double) $row->getAttribute("convCost")) / EXCHANGE_RATE));
        if ($row->getAttribute("lostImpShareBudget")) $row->setAttribute("lostImpShareBudget", ((String) ((double) $row->getAttribute("lostImpShareBudget")) / EXCHANGE_RATE));
        if ($row->getAttribute("conversionMaxCpa")) $row->setAttribute("conversionMaxCpa", ((String) ((double) $row->getAttribute("conversionMaxCpa")) / EXCHANGE_RATE));
        if ($row->getAttribute("maxCpp")) $row->setAttribute("maxCpp", ((String) ((double) $row->getAttribute("maxCpp")) / EXCHANGE_RATE));
        if ($row->getAttribute("agMaxCpa")) $row->setAttribute("agMaxCpa", ((String) ((double) $row->getAttribute("agMaxCpa")) / EXCHANGE_RATE));
        if ($row->getAttribute("preferredCpm")) $row->setAttribute("preferredCpm", ((String) ((double) $row->getAttribute("preferredCpm")) / EXCHANGE_RATE));
        if ($row->getAttribute("preferredCpc")) $row->setAttribute("preferredCpc", ((String) ((double) $row->getAttribute("preferredCpc")) / EXCHANGE_RATE));
        if ($row->getAttribute("maxContentCpc")) $row->setAttribute("maxContentCpc", ((String) ((double) $row->getAttribute("maxContentCpc")) / EXCHANGE_RATE));
        if ($row->getAttribute("maxCpm")) $row->setAttribute("maxCpm", ((String) ((double) $row->getAttribute("maxCpm")) / EXCHANGE_RATE));
        if ($row->getAttribute("budget")) $row->setAttribute("budget", ((String) ((double) $row->getAttribute("budget")) / EXCHANGE_RATE));
        if ($row->getAttribute("cost")) $row->setAttribute("cost", ((String) ((double) $row->getAttribute("cost")) / EXCHANGE_RATE));
        if ($row->getAttribute("cpc")) $row->setAttribute("cpc", ((String) ((double) $row->getAttribute("cpc")) / EXCHANGE_RATE));
        if ($row->getAttribute("maxCpc")) $row->setAttribute("maxCpc", ((String) ((double) $row->getAttribute("maxCpc")) / EXCHANGE_RATE));
        if ($row->getAttribute("cpm")) $row->setAttribute("cpm", ((String) ((double) $row->getAttribute("cpm")) / EXCHANGE_RATE));
        if ($row->getAttribute("firstPageCpc")) $row->setAttribute("firstPageCpc", ((String) ((double) $row->getAttribute("firstPageCpc")) / EXCHANGE_RATE));        
      }
      // in grandtotal
      $grandtotals = $xmlDomDocument->getElementsByTagName("grandtotal");
      foreach ($grandtotals as $grandtotal) {
        if ($grandtotal->getAttribute("cost")) $grandtotal->setAttribute("cost", ((String) ((double) $grandtotal->getAttribute("cost")) / EXCHANGE_RATE));
        if ($grandtotal->getAttribute("cpc")) $grandtotal->setAttribute("cpc", ((String) ((double) $grandtotal->getAttribute("cpc")) / EXCHANGE_RATE));
        if ($grandtotal->getAttribute("cpm")) $grandtotal->setAttribute("cpm", ((String) ((double) $grandtotal->getAttribute("cpm")) / EXCHANGE_RATE));
      }
      // in grandtotal
      $subtotals = $xmlDomDocument->getElementsByTagName("subtotal");
      foreach ($subtotals as $subtotal) {
        if ($subtotal->getAttribute("cost")) $subtotal->setAttribute("cost", ((String) ((double) $subtotal->getAttribute("cost")) / EXCHANGE_RATE));
        if ($subtotal->getAttribute("cost")) $subtotal->setAttribute("cpc", ((String) ((double) $subtotal->getAttribute("cpc")) / EXCHANGE_RATE));
        if ($subtotal->getAttribute("cost")) $subtotal->setAttribute("cpm", ((String) ((double) $subtotal->getAttribute("cpm")) / EXCHANGE_RATE));
      }
      return $xmlDomDocument->saveXML();
    }
    // PHP version is <5, i.e. only DOM XML is available
    else {
      $xmlDomDocument = domxml_open_mem($reportXml);
      $report = $xmlDomDocument->document_element();
      $table =  $report->first_child();
      $rows = $table->last_child();
      $singleRows = $rows->child_nodes();
      // in rows
      foreach($singleRows as $row) {      
        if ($row->get_attribute("budgetAmount")) $row->set_attribute("budgetAmount", ((String) ((double) $row->get_attribute("budgetAmount")) / EXCHANGE_RATE));
        if ($row->get_attribute("cpmHeads")) $row->set_attribute("cpmHeads", ((String) ((double) $row->get_attribute("cpmHeads")) / EXCHANGE_RATE));
        if ($row->get_attribute("costPerHead")) $row->set_attribute("costPerHead", ((String) ((double) $row->get_attribute("costPerHead")) / EXCHANGE_RATE));
        if ($row->get_attribute("estimatedVideoCpm")) $row->set_attribute("estimatedVideoCpm", ((String) ((double) $row->get_attribute("estimatedVideoCpm")) / EXCHANGE_RATE));
        if ($row->get_attribute("cpt")) $row->set_attribute("cpt", ((String) ((double) $row->get_attribute("cpt")) / EXCHANGE_RATE));
        if ($row->get_attribute("costPerConv")) $row->set_attribute("costPerConv", ((String) ((double) $row->get_attribute("costPerConv")) / EXCHANGE_RATE));
        if ($row->get_attribute("convCost")) $row->set_attribute("convCost", ((String) ((double) $row->get_attribute("convCost")) / EXCHANGE_RATE));
        if ($row->get_attribute("lostImpShareBudget")) $row->set_attribute("lostImpShareBudget", ((String) ((double) $row->get_attribute("lostImpShareBudget")) / EXCHANGE_RATE));
        if ($row->get_attribute("conversionMaxCpa")) $row->set_attribute("conversionMaxCpa", ((String) ((double) $row->get_attribute("conversionMaxCpa")) / EXCHANGE_RATE));
        if ($row->get_attribute("maxCpp")) $row->set_attribute("maxCpp", ((String) ((double) $row->get_attribute("maxCpp")) / EXCHANGE_RATE));
        if ($row->get_attribute("agMaxCpa")) $row->set_attribute("agMaxCpa", ((String) ((double) $row->get_attribute("agMaxCpa")) / EXCHANGE_RATE));
        if ($row->get_attribute("preferredCpm")) $row->set_attribute("preferredCpm", ((String) ((double) $row->get_attribute("preferredCpm")) / EXCHANGE_RATE));
        if ($row->get_attribute("preferredCpc")) $row->set_attribute("preferredCpc", ((String) ((double) $row->get_attribute("preferredCpc")) / EXCHANGE_RATE));
        if ($row->get_attribute("maxContentCpc")) $row->set_attribute("maxContentCpc", ((String) ((double) $row->get_attribute("maxContentCpc")) / EXCHANGE_RATE));
        if ($row->get_attribute("maxCpm")) $row->set_attribute("maxCpm", ((String) ((double) $row->get_attribute("maxCpm")) / EXCHANGE_RATE));
        if ($row->get_attribute("budget")) $row->set_attribute("budget", ((String) ((double) $row->get_attribute("budget")) / EXCHANGE_RATE));
        if ($row->get_attribute("cost")) $row->set_attribute("cost", ((String) ((double) $row->get_attribute("cost")) / EXCHANGE_RATE));
        if ($row->get_attribute("cpc")) $row->set_attribute("cpc", ((String) ((double) $row->get_attribute("cpc")) / EXCHANGE_RATE));
        if ($row->get_attribute("maxCpc")) $row->set_attribute("maxCpc", ((String) ((double) $row->get_attribute("maxCpc")) / EXCHANGE_RATE));
        if ($row->get_attribute("cpm")) $row->set_attribute("cpm", ((String) ((double) $row->get_attribute("cpm")) / EXCHANGE_RATE));        
        if ($row->get_attribute("firstPageCpc")) $row->set_attribute("firstPageCpc", ((String) ((double) $row->get_attribute("firstPageCpc")) / EXCHANGE_RATE));                
      }
      // in grandtotal
      $grandtotals = $xmlDomDocument->get_elements_by_tagname("grandtotal");
      foreach ($grandtotals as $grandtotal) {
        if ($grandtotal->get_attribute("cost")) $grandtotal->set_attribute("cost", ((String) ((double) $grandtotal->get_attribute("cost")) / EXCHANGE_RATE));
        if ($grandtotal->get_attribute("cpc")) $grandtotal->set_attribute("cpc", ((String) ((double) $grandtotal->get_attribute("cpc")) / EXCHANGE_RATE));
        if ($grandtotal->get_attribute("cpm")) $grandtotal->set_attribute("cpm", ((String) ((double) $grandtotal->get_attribute("cpm")) / EXCHANGE_RATE));
      }
      // in grandtotal
      $subtotals = $xmlDomDocument->get_elements_by_tagname("subtotal");
      foreach ($subtotals as $subtotal) {
        if ($subtotal->get_attribute("cost")) $subtotal->set_attribute("cost", ((String) ((double) $subtotal->get_attribute("cost")) / EXCHANGE_RATE));
        if ($subtotal->get_attribute("cpc")) $subtotal->set_attribute("cpc", ((String) ((double) $subtotal->get_attribute("cpc")) / EXCHANGE_RATE));
        if ($subtotal->get_attribute("cpm")) $subtotal->set_attribute("cpm", ((String) ((double) $subtotal->get_attribute("cpm")) / EXCHANGE_RATE));
      }
      // finished conversion
      return $xmlDomDocument->dump_mem();
    }
  }

  function deleteReport($reportJobId) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getReportClient();
    $soapParameters = "<deleteReport>
                         <reportJobId>" . $reportJobId . "</reportJobId>
                       </deleteReport>";
    // delete the report on the google servers
    $someSoapClient->call("deleteReport", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":deleteReport()", $soapParameters);
      return false;
    }
    return true;
  }

  function getAllJobs() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getReportClient();
    $soapParameters = "<getAllJobs></getAllJobs>";
    // query the google servers for all existing report jobs
    $allReportJobs = $someSoapClient->call("getAllJobs", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getAllJobs()", $soapParameters);
      return false;
    }
    return $allReportJobs['getAllJobsReturn'];
  }

  function xml2Tsv($xmlString, $onlyReturnDownloadUrl = false) {
    if (!$xmlString) return false;
    if (isset($onlyReturnDownloadUrl) && $onlyReturnDownloadUrl) {
      return $xmlString;  
    }
    // define tsv which will hold the tsv string
    $tsv = '';

    // an XML2TSV transformer
    // using DOM XML for downwards compatibility with PHP4
    //
    // structure of XML report is
    //
    // report
    //  |
    //  |-table
    //  |  |
    //  |  |-columns // contains column elements with the names of the report
    //  |  |         // columns like "campaign", "adgroup", "keyword", ...
    //  |  |
    //  |  |-rows // contains row elements with the specified report data
    //  |
    //  |-totals
    //     |
    //     |-subtotal
    //     |-grandtotal // contains general statistic information of the report

    // PHP version is >= 5, i.e. only DOM is avalable
    if (version_compare(phpversion(), "5.0.0", ">=")) {
      $xmlDomDocument = new DOMDocument();
      $xmlDomDocument->loadXML($xmlString);

      $singleColumns = $xmlDomDocument->getElementsByTagName("column");
      // get attribute names (i.e. column names)
      $attributeNames = array();
      // first line of the tsv report holds the column names
      foreach($singleColumns as $column) {
        $tsv .= $column->getAttribute('name') . "\t";
        array_push($attributeNames, $column->getAttribute('name'));
      }
      $tsv .= "\n";

      // fill columns with report data
      $singleRows = $xmlDomDocument->getElementsByTagName("row");
      foreach($singleRows as $row) {
        foreach($attributeNames as $attributeName) {
          $tsv .= $row->getAttribute($attributeName) . "\t";
        }
        $tsv .= "\n";
      }
      // and done
      return $tsv;
    }
    // PHP version is <5, i.e. only DOM XML is available
    else {
      $xmlDomDocument = domxml_open_mem($xmlString);
      $report = $xmlDomDocument->document_element();
      $table =  $report->first_child();
      $totals = $report->last_child();
      $columns = $table->first_child();
      $rows = $table->last_child();
      // might add grandtotal but won't do this at present
      // uncomment the following line to do this anyhow
      //$grandtotal = $totals->first_child();

      $singleColumns = $columns->child_nodes();

      // get attribute names (i.e. column names)
      $attributeNames = array();
      // first line of the tsv report holds the column names
      foreach($singleColumns as $column) {
        $tsv .= $column->get_attribute('name') . "\t";
        array_push($attributeNames, $column->get_attribute('name'));
      }
      $tsv .= "\n";

      // fill columns with report data
      $singleRows = $rows->child_nodes();
      foreach($singleRows as $row) {
        foreach($attributeNames as $attributeName) {
          $tsv .= $row->get_attribute($attributeName) . "\t";
        }
        $tsv .= "\n";
      }
      // and done
      return $tsv;
    }
  }

  function validateReportJob($reportXml) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getReportClient();
    $soapParameters = "<validateReportJob xmlns='" . REPORT_WSDL_URL . "'>" . 
                         $reportXml . "
                       </validateReportJob>";
    // query the google servers for all existing report jobs
    $validation = $someSoapClient->call("validateReportJob", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":validateReportJob()", $soapParameters);
      return false;
    }
    return true;    
  }

  function generateCurlUserAgent() {
    // the google servers return the reports transparently (on the ISO/OSI
    // transport layer) gzipped if the header contains the string "gzip"
    $curlVersion = curl_version();
    // PHP version is >= 5
    if (version_compare(phpversion(), "5.0.0", ">="))  {
      $userAgent =
          "libcurl/" . $curlVersion['version'].$curlVersion['ssl_version'] .
          " libz/" . $curlVersion['libz_version'];
    }
    else {
    // PHP version is <5
      $userAgent = $curlVersion;
    }
    return $userAgent;
  }
?>
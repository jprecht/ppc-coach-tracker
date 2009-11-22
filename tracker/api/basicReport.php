<?php
/**
 * The BasicReportRequest object represents a request to run or generate a report. 
 * Various types of reports are available at the account, campaign, ad group, and keyword level.
 *
 * @package ewsPHP
 */
class ewsBasicReportRequest{
	/**#@+
	 * @access private
	 */
	var $dateRange; /* Last30Days, Last7Days, LastBusinessWeek, LastCalendarMonth, LastCalendarQuarter, LastCalendarWeek, MonthToDate, WeekToDate, YearToDate, Yestarday */
	var $endDate;
	var $reportName;
	var $reportType; /* AccountSummary, AdGroupSummary, CampaignSummary, DailySummary, KeywordSummary, and MultiChannel* */
	var $startDate;
	/**#@-*//**
	 * The BasicReportRequest object represents a request to run or generate a report. 
 	 * Various types of reports are available at the account, campaign, ad group, and keyword level.
	 *
	 * @param string $dateRange [Last30Days, Last7Days, LastBusinessWeek, LastCalendarMonth, LastCalendarQuarter, LastCalendarWeek, MonthToDate, WeekToDate, YearToDate, Yestarday]
	 * @param date $endDate
	 * @param string $reportName
	 * @param string $reportType [AccountSummary, AdGroupSummary, CampaignSummary, DailySummary, KeywordSummary, and MultiChannel*]
	 * @param date $startDate
	 * @access private
	 * @return ewsBasicReportRequest
	 */

	
	function ewsBasicReportRequest($dateRange, $endDate, $reportName, $reportType, $startDate){
		$this->dateRange = $dateRange;
		$this->endDate = $endDate;
		$this->reportName = $reportName;
		$this->reportType = $reportType;
		$this->startDate = $startDate;
	}

	/**
	 * Return the object property - dateRange
	 *
	 * @return string
	 */
	function getDateRange(){
		return $this->dateRange;
	}
	/**
	 * Return the object property - endDate
	 *
	 * @return date
	 */
	function getEndDate(){
		return $this->endDate;
	}
	/**
	 * Return the object property - reportName
	 *
	 * @return string
	 */
	function getReportName(){
		return $this->reportName;
	}
	/**
	 * Return the object property - reportType
	 *
	 * @return string
	 */
	function getReportType(){
		return $this->reportType;
	}
	/**
	 * Return the object property - startDate
	 *
	 * @return date
	 */
	function getStartDate(){
		return $this->startDate;
	}
}
/**
 * The ReportInfo object represents a report.
 *
 * @package ewsPHP
 */
class ewsReportInfo{
	/**#@+
	 * @access private
	 */
	var $createDate;
	var $reportID;
	var $reportName;
	var $status;
	/**#@-*/
	
	/**
	 * The ReportInfo object represents a report.
	 *
	 * @param dateTime $createDate
	 * @param int $reportID
	 * @param string $reportName
	 * @param string $status
	 * @access private
	 * @return ewsReportInfo
	 */
	function ewsReportInfo($createDate, $reportID, $reportName, $status) {
		$this->createDate = $createDate;
		$this->reportID = $reportID;
		$this->reportName = $reportName;
		$this->status = $status;
	}
	
	/**
	 * Return the object property - createDate
	 *
	 * @return dateTime
	 */
	function getCreateDate(){
		return $this->createDate;
	}
	/**
	 * Return the object property - reportID
	 *
	 * @return int
	 */
	function getReportID(){
		return $this->reportID;
	}
	/**
	 * Return the object property - reportName
	 *
	 * @return string
	 */
	function getReportName(){
		return $this->reportName;
	}
	/**
	 * Return the object property - status
	 *
	 * @return string
	 */
	function getStatus(){
		return $this->status;
	}
}
?>
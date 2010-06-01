<?php
session_start();
$_SESSION['language'] = "no";
$_SESSION['show-errors'] = false;
if (!isset($_SESSION['tinyMCE']['theme'])){
	$_SESSION['tinyMCE']['theme'] = "simple";
}
ini_set('display_errors', 1);

$locale = Array('no_NO', 'nor_nor');
setlocale(LC_TIME, $locale);

//Extras
require_once "functions.php";
require_once "language.php";

set_include_path("../includes");

$include_path = "../includes/";

//PEAR-class
require_once $include_path."DB.php";

//Local classes
require_once "Action.php";
require_once "Actions.php";
require_once "ActionGroupRelationship.php";
require_once "ActionGroupRelationships.php";
require_once "ActionParser.php";
require_once "Article.php";
require_once "Articles.php";
require_once "BarShift.php";
require_once "BarShifts.php";
require_once "BarShiftWorker.php";
require_once "BarShiftWorkers.php";
require_once "BugReport.php";
require_once "BugReports.php";
require_once "Calendar.php";
require_once "Concert.php";
require_once "Concerts.php";
require_once "ConcertCategories.php";
require_once "ConcertReport.php";
require_once "Division.php";
require_once "Divisions.php";
require_once "DivisionCategories.php";
require_once "Document.php";
require_once "Documents.php";
require_once "DocumentCategory.php";
require_once "DocumentCategories.php";
require_once "Event.php";
require_once "Events.php";
require_once "EventCategory.php";
require_once "EventCategories.php";
require_once "EventComment.php";
require_once "EventComments.php";
require_once "Errors.php";
require_once "Form.php";
require_once "Group.php";
require_once "Groups.php";
require_once "Job.php";
require_once "Jobs.php";
require_once "JobCategory.php";
require_once "JobCategories.php";
require_once "Locations.php";
require_once "Messages.php";
require_once "MemberCard.php";
require_once "MembershipActivationCode.php";
require_once "Navigation.php";
require_once "Nordea.php";
require_once "Order.php";
//require_once "Orders.php";
require_once "OrderStatuses.php";
require_once "OrderItem.php";
require_once "Page.php";
require_once "Payment.php";
require_once "PlacesOfStudy.php";
require_once "Position.php";
require_once "Positions.php";
require_once "Product.php";
require_once "Products.php";
require_once "ProgramSelection.php";
require_once "ProgramsSelection.php";
require_once "Transaction.php";
//if (getcurrentuser() == 2460) {
//  require_once "User_new.php";
//} else {
  require_once "User.php";
//}
require_once "Users.php";
require_once "UserGroupRelationship.php";
require_once "UserGroupRelationships.php";
require_once "WeekProgram.php";
require_once "WeekPrograms.php";

//Errors
if (isAdmin()){
  error_reporting(E_ALL);
}else {
  error_reporting(0);
}

//cookies
$page = scriptParam("page");

$userid = scriptParam("userid");
if (!empty($userid)){
  @setcookie(getCurrentUser()."[userid]", $userid);
}
$limit = scriptParam("limit");
if (!empty($limit)){
  @setcookie(getCurrentUser()."[limit]", $limit);
}
$groupid = scriptParam("groupid");
if (!empty($groupid)){
  @setcookie(getCurrentUser()."[groupid]", $groupid);
}
$divisionid = scriptParam("divisionid");
if (!empty($divisionid)){
  @setcookie(getCurrentUser()."[divisionid]", $divisionid);
}
$expiry = scriptParam("expiry");
if (!empty($expiry)){
  @setcookie(getCurrentUser()."[expiry]", $expiry);
}
$format = scriptParam("format");
if (!empty($format)){
  @setcookie(getCurrentUser()."[format]", $format);
}
$documentcategoryid = scriptParam("documentcategoryid");
if (!empty($documentcategoryid)){
  @setcookie(getCurrentUser()."[documentcategoryid]", $documentcategoryid);
}

$month = scriptParam("month");
if (!empty($month)){
  @setcookie(getCurrentUser()."[month]", $month);
}

$orderid = scriptParam("order_id");
if (!empty($orderid)){
  @setcookie(getCurrentUser()."[order_id]", $orderid);
}

if ($page == "display-barshifts" || $page == "display-barshifts-calendar") {
 	@setcookie(getCurrentUser()."[barshift-overview]", $page);
}

//Sections
$sections = Array("display-barshifts" => "barshifts",
									"change-username" => "quick",
									"display-action" => "access",
									"display-actiongrouprelationships" => "access",
									"display-actions" => "access",
									"display-all-calendar" => "",
									"display-article" => "webpages",
									"display-articles" => "webpages",
									"display-barshift" => "barshifts",
									"display-barshifts" => "barshifts",
									"display-barshifts-calendar" => "barshifts",
									"display-bugreport" => "messages",
									"display-bugreports" => "messages",
									"display-cart" => "webshop",
									"display-carts" => "webshop",
									"display-concert" => "concerts",
									"display-concerts" => "concerts",
									"display-concerts-calendar" => "concerts",
									"display-current-user" => "quick",
									"display-division" => "divisions",
									"display-division-requests" => "users",
									"display-divisions" => "divisions",
									"display-documents" => "documents",
									"display-event" => "events",
									"display-events" => "events",
									"display-events-archive" => "events",
									"display-events-calendar" => "events",
									"display-group" => "access",
									"display-groups" => "access",
									"display-job" => "jobs",
									"display-jobs" => "jobs",
									"display-jobs-archive" => "jobs",
									"display-position" => "divisions",
									"display-positions" => "divisions",
									"display-product" => "webshop",
									"display-sales" => "webshop",
									"display-sales-item" => "webshop",
									"display-user" => "users",
									"display-user-expiries" => "users",
									"display-usergrouprelationships" => "users",
									"display-users" => "users",
									"display-users-study-place" => "users",
									"display-webshop" => "webshop",
									"edit-action" => "access",
									"edit-actiongrouprelationship" => "access",
									"edit-article" => "webpages",
									"edit-barshift" => "barshifts",
									"edit-category" => "settings",
									"edit-concert" => "concerts",
									"edit-division" => "divisions",
									"edit-documentcategory" => "settings",
									"edit-event" => "events",
									"edit-eventcategory" => "settings",
									"edit-group" => "access",
									"edit-job" => "jobs",
									"edit-jobcategory" => "settings",
									"edit-membership" => "users",
									"edit-position" => "divisions",
									"edit-product" => "webshop",
									"edit-user" => "users",
									"edit-usergrouprelationship" => "users",
									"membercard-production" => "billettbod",
									"membership-sale" => "billettbod",
                  "payex-form" => "users",
									"register-action" => "access",
									"register-actiongrouprelationship" => "access",
									"register-article" => "webpages",
									"register-barshift" => "barshifts",
									"register-category" => "settings",
									"register-concert" => "concerts",
									"register-division" => "divisions",
									"register-documentcategory" => "settings",
									"register-user-ea-update" => "users",
									"register-event" => "events",
									"register-eventcategory" => "settings",
									"register-group" => "access",
									"register-job" => "jobs",
									"register-jobcategory" => "settings",
									"register-membership" => "quick",
                                    "register-membership-bankpayment" => "users",
									"register-position" => "divisions",
									"register-product" => "settings",
									"register-user" => "users",
									"register-usergrouprelationship" => "users",
									"renew-membership" => "users",
									"reset-password" => "",
									"upload-document" => "documents",
									"week-program" => "concerts"
									);

?>
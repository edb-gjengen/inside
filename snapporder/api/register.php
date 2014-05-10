<?php 
/* 
 * Registers a temporary user in the temp user table
 *
 * Request:
 *
 * $ curl /snapporder/api/register.php 
 * {
 *   "phone":12345678,
 *   "firstName": "Jon",
 *   "lastName": "Hansen",
 *   "email": "jon@uio.no"
 * }
 *
 * Response:
 *
 * {
 *   "phone":12345678,
 *   "memberStatus":1,
 *   "endDate": "2015-04-23" ,
 *   "membershipNumber": "dsadsa2333",
 *   "firstName": "Jon",
 *   "lastName": "Hansen",
 *   "email": "jon@uio.no"
 * }
 *
 * TODO:
 * - Decode encrypted http body
 * - validate http body
 * - register user in temp user db
 * - return result
 */
?>

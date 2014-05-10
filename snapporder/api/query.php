<?php
/* 
 * Lookup a members phone number and return a member-like object.
 *
 * Request:
 *
 * $ curl /snapporder/api/query.php?phone=48105885
 *
 * Response:
 *
 * {
 *   "phone":12345678,
 *   "memberStatus":1, // 0 - ikke medlem, 1 - medlem, 2 - aktiv
 *   "endDate": "2015-04-23",
 *   "membershipNumber": "dsadsa2333",
 *   "firstName": "Jon",
 *   "lastName": "Hansen",
 *   "email": "jon@uio.no"
 * }
 *
 * TODO:
 * - Decode http body
 * - validate and sanitize query param
 * - lookup up phone number
 * - return result
 */

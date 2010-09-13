<?php

/**
 * Membership collection - collection of elements in the user database
 *
 * @version 0.1
 */

class mapper_MembershipCollection extends mapper_Collection {
  function targetClass() {
    return "domain_Membership";
  }
}
?>
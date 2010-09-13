<?php

/**
 * Helper factory for fetching collections from the database mapper
 *
 * @version 0.1
 */

class domain_HelperFactory {
  const MEMBERSHIP = "domain_Membership";
  const MEMBERSHIPCARD = "domain_MembershipCard";
  const USER = "domain_User";
  const USERADDRESS = "domain_UserAddress";
  const USERUPDATE = "domain_UserUpdate";

  function getCollection($type) {
    switch ($type) {
      case (self::MEMBERSHIP):
        return new mapper_MembershipCollection();
      case (self::MEMBERSHIPCARD):
        return new mapper_MembershipCardCollection();
      case (self::USER):
        return new mapper_UserCollection();
      case (self::USERADDRESS):
        return new mapper_UserAddressCollection();
      case (self::USERUPDATE):
        return new mapper_UserUpdateCollection();
      default:
        throw new Exception("Unknown collection type $type");
    }
  }

  function getFinder($type) {
    switch ($type) {
      case (self::MEMBERSHIP):
        return new mapper_MembershipMapper();
      case (self::MEMBERSHIPCARD):
        return new mapper_MembershipCardMapper();
      case (self::USER):
        return new mapper_UserMapper();
      case (self::USERADDRESS):
        return new mapper_UserAddressMapper();
      case (self::USERUPDATE):
        return new mapper_UserUpdateMapper();
      default:
        throw new Exception("Unknown finder type $type");
    }
  }
}

?>
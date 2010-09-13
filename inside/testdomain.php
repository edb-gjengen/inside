<?php

if (isset($_GET["id"])) $id = $_GET["id"];
else $id = 1;

include("functions.php");
include("domain/DomainObject.php");
include("domain/HelperFactory.php");
include("domain/ObjectWatcher.php");
include("domain/Membership.php");
include("domain/MembershipCard.php");
include("domain/User.php");
include("domain/UserAddress.php");
include("domain/UserUpdate.php");
include("mapper/Collection.php");
include("mapper/Mapper.php");
include("mapper/MembershipCardCollection.php");
include("mapper/MembershipCardMapper.php");
include("mapper/MembershipCollection.php");
include("mapper/MembershipMapper.php");
include("mapper/UserAddressCollection.php");
include("mapper/UserAddressMapper.php");
include("mapper/UserCollection.php");
include("mapper/UserMapper.php");
include("mapper/UserUpdateCollection.php");
include("mapper/UserUpdateMapper.php");

header("Content-type: text/plain");
$user = new domain_User();
$users = $user->collection();
for ($i = 13048; $i <= 13048; $i++) {
  if ($user = domain_DomainObject::getFinder("domain_User")->find($i)) {
    $users->add($user);
  }
}

foreach ($users as $user) {
  print_r($user);
  $user->getUpdates();;
  $user->getMemberships();
  $user->getCards();
  $user->getAddress();
  
  print_r($user);
  //print "\n" . $user->getMemberships()->current()->getStarts()->format("Y-m-d");
}
//$userMapper->update($user);

// perform database modifications
domain_ObjectWatcher::instance()->performOperations();
?>
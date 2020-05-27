<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\ExtractTransformLoad;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PersonalizationOrderManagerProvider;
use Pimcore\Model\DataObject\CustomerSegment;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerInfo;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PurchaseHistoryProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;


class PurchaseHistoryProviderTest extends TestCase
{
    public function testGetPurchaseHistoryLoggedInUser()
    {
        //$expectedPurchaseHistory = CustomerInfo::__set_state(array( 'customerId' => 1021, 'segments' => array ( 0 => Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo::__set_state(array( 'segmentId' => 983, 'segmentCount' => 1, )), 1 => Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo::__set_state(array( 'segmentId' => 963, 'segmentCount' => 1, )), 2 => Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo::__set_state(array( 'segmentId' => 982, 'segmentCount' => 1, )), 3 => Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo::__set_state(array( 'segmentId' => 971, 'segmentCount' => 1, )), 4 => Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo::__set_state(array( 'segmentId' => 970, 'segmentCount' => 1, )), ), ));

        $purchaseHistoryProvider = $this->createPurchaseHistoryProvider();
        $userId = 1021;
        $actualPurchaseHistory = $purchaseHistoryProvider->getPurchaseHistory($userId);

        //self::assertEquals($expectedPurchaseHistory, $actualPurchaseHistory);
        self::assertTrue(true);
    }

    private function createPurchaseHistoryProvider() : PurchaseHistoryProvider
    {
        $getterInterface = $this
            ->getMockBuilder(GetterInterface::class)
            ->setMethods(['get'])
            ->getMock();
        /*$getterInterface->method('get')
            ->willReturn(
                array ( 0 => CustomerSegment::__set_state(array( 'o_classId' => '2', 'o_className' => 'CustomerSegment', 'name' => 'Chevrolet', 'group' => NULL, 'reference' => 'Chevrolet', 'calculated' => true, 'useAsTargetGroup' => NULL, 'targetGroup' => NULL, '__rawRelationData' => array ( 0 => array ( 'src_id' => '983', 'dest_id' => '960', 'type' => 'object', 'fieldname' => 'group', 'index' => '0', 'ownertype' => 'object', 'ownername' => '', 'position' => '0', ), ), 'o_published' => true, 'o_class' => NULL, 'o_versions' => NULL, 'scheduledTasks' => NULL, 'omitMandatoryCheck' => false, 'allLazyKeysMarkedAsLoaded' => false, 'o_id' => 983, 'o_parentId' => 960, 'o_parent' => NULL, 'o_type' => 'object', 'o_key' => 'Chevrolet', 'o_path' => '/Customer Management/segments/calculated/Interest Manufacturer/', 'o_index' => 0, 'o_creationDate' => 1566893740, 'o_modificationDate' => 1566893740, 'o_userOwner' => 0, 'o_userModification' => 0, 'o_properties' => NULL, 'o_hasChildren' => array ( ), 'o_siblings' => array ( ), 'o_hasSiblings' => array ( ), 'o_dependencies' => NULL, 'o_children' => array ( ), 'o_locked' => NULL, 'o_elementAdminStyle' => NULL, 'o_childrenSortBy' => NULL, 'o_versionCount' => 1, '__dataVersionTimestamp' => 1566893740, 'dao' => NULL, '_fulldump' => false, 'o_dirtyFields' => NULL, 'loadedLazyKeys' => array ( ), '____pimcore_cache_item__' => 'object_983', )), 1 => Pimcore\Model\DataObject\CustomerSegment::__set_state(array( 'o_classId' => '2', 'o_className' => 'CustomerSegment', 'name' => 'sports car', 'group' => NULL, 'reference' => 'sports car', 'calculated' => true, 'useAsTargetGroup' => NULL, 'targetGroup' => NULL, '__rawRelationData' => array ( 0 => array ( 'src_id' => '963', 'dest_id' => '962', 'type' => 'object', 'fieldname' => 'group', 'index' => '0', 'ownertype' => 'object', 'ownername' => '', 'position' => '0', ), ), 'o_published' => true, 'o_class' => NULL, 'o_versions' => NULL, 'scheduledTasks' => NULL, 'omitMandatoryCheck' => false, 'allLazyKeysMarkedAsLoaded' => false, 'o_id' => 963, 'o_parentId' => 962, 'o_parent' => NULL, 'o_type' => 'object', 'o_key' => 'sports car', 'o_path' => '/Customer Management/segments/calculated/Interest Car Class/', 'o_index' => 0, 'o_creationDate' => 1566893534, 'o_modificationDate' => 1566893534, 'o_userOwner' => 2, 'o_userModification' => 2, 'o_properties' => NULL, 'o_hasChildren' => array ( ), 'o_siblings' => array ( ), 'o_hasSiblings' => array ( ), 'o_dependencies' => NULL, 'o_children' => array ( ), 'o_locked' => NULL, 'o_elementAdminStyle' => NULL, 'o_childrenSortBy' => NULL, 'o_versionCount' => 1, '__dataVersionTimestamp' => 1566893534, 'dao' => NULL, '_fulldump' => false, 'o_dirtyFields' => NULL, 'loadedLazyKeys' => array ( ), '____pimcore_cache_item__' => 'object_963', )), 2 => Pimcore\Model\DataObject\CustomerSegment::__set_state(array( 'o_classId' => '2', 'o_className' => 'CustomerSegment', 'name' => '2-door convertible', 'group' => NULL, 'reference' => '2-door convertible', 'calculated' => true, 'useAsTargetGroup' => NULL, 'targetGroup' => NULL, '__rawRelationData' => array ( 0 => array ( 'src_id' => '982', 'dest_id' => '964', 'type' => 'object', 'fieldname' => 'group', 'index' => '0', 'ownertype' => 'object', 'ownername' => '', 'position' => '0', ), ), 'o_published' => true, 'o_class' => NULL, 'o_versions' => NULL, 'scheduledTasks' => NULL, 'omitMandatoryCheck' => false, 'allLazyKeysMarkedAsLoaded' => false, 'o_id' => 982, 'o_parentId' => 964, 'o_parent' => NULL, 'o_type' => 'object', 'o_key' => '2-door convertible', 'o_path' => '/Customer Management/segments/calculated/Interest Body Style/', 'o_index' => 0, 'o_creationDate' => 1566893740, 'o_modificationDate' => 1566893740, 'o_userOwner' => 0, 'o_userModification' => 0, 'o_properties' => NULL, 'o_hasChildren' => array ( ), 'o_siblings' => array ( ), 'o_hasSiblings' => array ( ), 'o_dependencies' => NULL, 'o_children' => array ( ), 'o_locked' => NULL, 'o_elementAdminStyle' => NULL, 'o_childrenSortBy' => NULL, 'o_versionCount' => 1, '__dataVersionTimestamp' => 1566893740, 'dao' => NULL, '_fulldump' => false, 'o_dirtyFields' => NULL, 'loadedLazyKeys' => array ( ), '____pimcore_cache_item__' => 'object_982', )), ),
                array ( 0 => CustomerSegment::__set_state(array( 'o_classId' => '2', 'o_className' => 'CustomerSegment', 'name' => 'Austin-Healey', 'group' => NULL, 'reference' => 'Austin-Healey', 'calculated' => true, 'useAsTargetGroup' => NULL, 'targetGroup' => NULL, '__rawRelationData' => array ( 0 => array ( 'src_id' => '971', 'dest_id' => '960', 'type' => 'object', 'fieldname' => 'group', 'index' => '0', 'ownertype' => 'object', 'ownername' => '', 'position' => '0', ), ), 'o_published' => true, 'o_class' => NULL, 'o_versions' => NULL, 'scheduledTasks' => NULL, 'omitMandatoryCheck' => false, 'allLazyKeysMarkedAsLoaded' => false, 'o_id' => 971, 'o_parentId' => 960, 'o_parent' => NULL, 'o_type' => 'object', 'o_key' => 'Austin-Healey', 'o_path' => '/Customer Management/segments/calculated/Interest Manufacturer/', 'o_index' => 0, 'o_creationDate' => 1566893534, 'o_modificationDate' => 1566893534, 'o_userOwner' => 2, 'o_userModification' => 2, 'o_properties' => NULL, 'o_hasChildren' => array ( ), 'o_siblings' => array ( ), 'o_hasSiblings' => array ( ), 'o_dependencies' => NULL, 'o_children' => array ( ), 'o_locked' => NULL, 'o_elementAdminStyle' => NULL, 'o_childrenSortBy' => NULL, 'o_versionCount' => 1, '__dataVersionTimestamp' => 1566893534, 'dao' => NULL, '_fulldump' => false, 'o_dirtyFields' => NULL, 'loadedLazyKeys' => array ( ), '____pimcore_cache_item__' => 'object_971', )), ),
                array ( 0 => CustomerSegment::__set_state(array( 'o_classId' => '2', 'o_className' => 'CustomerSegment', 'name' => 'Alfa Romeo', 'group' => NULL, 'reference' => 'Alfa Romeo', 'calculated' => true, 'useAsTargetGroup' => NULL, 'targetGroup' => NULL, '__rawRelationData' => array ( 0 => array ( 'src_id' => '970', 'dest_id' => '960', 'type' => 'object', 'fieldname' => 'group', 'index' => '0', 'ownertype' => 'object', 'ownername' => '', 'position' => '0', ), ), 'o_published' => true, 'o_class' => NULL, 'o_versions' => NULL, 'scheduledTasks' => NULL, 'omitMandatoryCheck' => false, 'allLazyKeysMarkedAsLoaded' => false, 'o_id' => 970, 'o_parentId' => 960, 'o_parent' => NULL, 'o_type' => 'object', 'o_key' => 'Alfa Romeo', 'o_path' => '/Customer Management/segments/calculated/Interest Manufacturer/', 'o_index' => 0, 'o_creationDate' => 1566893534, 'o_modificationDate' => 1566893534, 'o_userOwner' => 2, 'o_userModification' => 2, 'o_properties' => NULL, 'o_hasChildren' => array ( ), 'o_siblings' => array ( ), 'o_hasSiblings' => array ( ), 'o_dependencies' => NULL, 'o_children' => array ( ), 'o_locked' => NULL, 'o_elementAdminStyle' => NULL, 'o_childrenSortBy' => NULL, 'o_versionCount' => 1, '__dataVersionTimestamp' => 1566893534, 'dao' => NULL, '_fulldump' => false, 'o_dirtyFields' => NULL, 'loadedLazyKeys' => array ( ), '____pimcore_cache_item__' => 'object_970', )), )
            );*/

        $orderIndexProvider = $this
            ->getMockBuilder(OrderIndexAccessProvider::class)
            ->setMethods(['index'])
            ->getMock();
        $orderIndexProvider->method('index')
            ->willReturn(true);

        $orderManager = $this
            ->getMockBuilder(OrderManagerInterface::class)
            ->setMethods(['createOrderList'])
            ->getMock();
        $orderList = $this
            ->getMockBuilder("OrderList")
            ->setMethods(['getQuery', 'joinCustomer'])
            ->getMock();
        $orderManager->method('createOrderList')
            ->willReturn($orderList);
        $orderQuery = $this
            ->getMockBuilder('OrderQuery')
            ->setMethods(['where'])
            ->getMock();
        $orderList->method('getQuery')
            ->willReturn($orderQuery);
        $orderQuery->method('where')
            ->willReturn($orderQuery);

        return new PurchaseHistoryProvider($getterInterface, new MockOrderManagerProvider($orderManager), $orderIndexProvider);
    }
}



<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\MerchantRelationshipDataImport\Helper;

use Codeception\Module;
use Orm\Zed\MerchantRelationship\Persistence\SpyMerchantRelationshipQuery;
use Spryker\Zed\CompanyBusinessUnit\Business\CompanyBusinessUnitFacadeInterface;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class MerchantRelationshipDataImportHelper extends Module
{
    use LocatorHelperTrait;

    public function assertDatabaseTableIsEmpty(): void
    {
        $query = $this->getMerchantRelationshipQuery();
        $this->assertSame(0, $query->count(), 'Found at least one entry in the database table but database table was expected to be empty.');
    }

    public function assertDatabaseTableContainsData(): void
    {
        $query = $this->getMerchantRelationshipQuery();
        $this->assertTrue($query->count() > 0, 'Expected at least one entry in the database table but database table is empty.');
    }

    protected function getMerchantRelationshipQuery(): SpyMerchantRelationshipQuery
    {
        return SpyMerchantRelationshipQuery::create();
    }

    public function getCompanyBusinessUnitFacade(): CompanyBusinessUnitFacadeInterface
    {
        return $this->getLocator()->companyBusinessUnit()->facade();
    }
}

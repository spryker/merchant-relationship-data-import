<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\MerchantRelationshipDataImport\Business\Model\Step;

use Orm\Zed\CompanyBusinessUnit\Persistence\Map\SpyCompanyBusinessUnitTableMap;
use Orm\Zed\CompanyBusinessUnit\Persistence\SpyCompanyBusinessUnitQuery;
use Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\MerchantRelationshipDataImport\Business\Model\DataSet\MerchantRelationshipDataSetInterface;

class CompanyBusinessUnitAssigneeKeysToIdCompanyBusinessUnitCollectionStep implements DataImportStepInterface
{
    /**
     * @phpstan-var non-empty-string
     *
     * @var string
     */
    protected $assigneeDelimiter = ';';

    /**
     * @var array
     */
    protected $idCompanyBusinessUnitCache = [];

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        if (!$dataSet[MerchantRelationshipDataSetInterface::COMPANY_BUSINESS_UNIT_ASSIGNEE_KEYS]) {
            $dataSet[MerchantRelationshipDataSetInterface::ID_COMPANY_BUSINESS_UNIT_ASSIGNEE_COLLECTION] = [];

            return;
        }

        /** @var array<string> $companyBusinessUnitKeys */
        $companyBusinessUnitKeys = explode(
            $this->getAssigneeDelimiter(),
            $dataSet[MerchantRelationshipDataSetInterface::COMPANY_BUSINESS_UNIT_ASSIGNEE_KEYS],
        );

        $companyBusinessUnitAssignee = [];
        foreach ($companyBusinessUnitKeys as $companyBusinessUnitKey) {
            $companyBusinessUnitAssignee[] = $this->findIdCompanyBusinessUnit($companyBusinessUnitKey);
        }

        $dataSet[MerchantRelationshipDataSetInterface::ID_COMPANY_BUSINESS_UNIT_ASSIGNEE_COLLECTION] = $companyBusinessUnitAssignee;
    }

    /**
     * @phpstan-return non-empty-string
     *
     * @return string
     */
    public function getAssigneeDelimiter(): string
    {
        return $this->assigneeDelimiter;
    }

    /**
     * @phpstan-param non-empty-string $assigneeDelimiter
     *
     * @param string $assigneeDelimiter
     *
     * @return void
     */
    public function setAssigneeDelimiter(string $assigneeDelimiter): void
    {
        $this->assigneeDelimiter = $assigneeDelimiter;
    }

    /**
     * @param string $companyBusinessUnitKey
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException
     *
     * @return int
     */
    protected function findIdCompanyBusinessUnit(string $companyBusinessUnitKey): int
    {
        if (!isset($this->idCompanyBusinessUnitCache[$companyBusinessUnitKey])) {
            $idCompanyBusinessUnit = SpyCompanyBusinessUnitQuery::create()
                ->select(SpyCompanyBusinessUnitTableMap::COL_ID_COMPANY_BUSINESS_UNIT)
                ->findOneByKey($companyBusinessUnitKey);

            if (!$idCompanyBusinessUnit) {
                throw new EntityNotFoundException(sprintf('Could not find Company Business Unit by key "%s"', $companyBusinessUnitKey));
            }

            $this->idCompanyBusinessUnitCache[$companyBusinessUnitKey] = $idCompanyBusinessUnit;
        }

        return $this->idCompanyBusinessUnitCache[$companyBusinessUnitKey];
    }
}

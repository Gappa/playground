<?php

declare(strict_types=1);

namespace App\Presenters;

use Dibi\Connection;
use Dibi\Row;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\Column\ColumnLink;
use Ublaboo\DataGrid\Column\ColumnStatus;
use Ublaboo\DataGrid\DataGrid;

final class FiltersPresenter extends Presenter
{

	/**
	 * @var Connection
	 * @inject
	 */
	public $dibiConnection;


	public function createComponentGrid(): DataGrid
	{
		$grid = new DataGrid;

		$grid->setDataSource($this->dibiConnection->select('*')->from('users'));

		$grid->setItemsPerPageList([20, 50, 100], true);

		$grid->addColumnText('id', '#')
			->setFilterText()
			->setExactSearch();

		$grid->addColumnText('name', 'Name')
			->setFilterText();

		$grid->addColumnLink('email', 'E-mail', 'this')
			->setFilterText();

		$grid->addColumnStatus('status', 'Status')
			->setFilterSelect([
				'' => 'All',
				'active' => 'Active',
				'inactive' => 'Inactive',
				'deleted' => 'Deleted',
			]);

		$grid->addColumnDateTime('birth_date', 'Birthday')
			->setFormat('j. n. Y')
			->setSortable()
			->setFilterDate();

		$grid->addColumnNumber('age', 'Age')
			->setRenderer(function(Row $row): int {
				return $row['birth_date']->diff(new \DateTime)->y;
			});

		return $grid;
	}


	/**
	 * @param mixed $id
	 */
	public function changeStatus($id, string $newStatus): void
	{
		$id = (int) $id;

		if (in_array($newStatus, ['active', 'inactive', 'deleted'], true)) {
			$data = ['status' => $newStatus];

			$this->dibiConnection->update('users', $data)
				->where('id = ?', $id)
				->execute();
		}

		if ($this->isAjax()) {
			$grid = $this['grid'];


			if (!$grid instanceof DataGrid) {
				throw new \UnexpectedValueException;
			}

			$grid->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}
}
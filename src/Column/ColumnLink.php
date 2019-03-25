<?php declare(strict_types = 1);

namespace Ublaboo\DataGrid\Column;

use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridColumnRendererException;
use Ublaboo\DataGrid\Row;

class ColumnLink extends Column
{

	/** @var string|null */
	protected $title;

	/** @var string|null */
	protected $class;

	/** @var array */
	protected $params;

	/** @var string */
	protected $href;

	/** @var string|null */
	protected $icon;

	/** @var array */
	protected $dataAttributes = [];

	/** @var bool */
	protected $openInNewTab = false;

	/** @var array */
	protected $parameters = [];

	public function __construct(
		DataGrid $grid,
		string $key,
		string $column,
		string $name,
		string $href,
		array $params
	)
    {
		parent::__construct($grid, $key, $column, $name);

		$this->href = $href;
		$this->params = $params;
	}


	/**
	 * @return mixed
	 */
	public function render(Row $row)
	{
		/**
		 * Renderer function may be used
		 */
		try {
			return $this->useRenderer($row);
		} catch (DataGridColumnRendererException $e) {
			/**
			 * Do not use renderer
			 */
		}

		$value = parent::render($row);

		if (!$value && !$this->icon) {
			return null;
		}

		$a = Html::el('a')
			->href($this->createLink(
				$this->grid,
				$this->href,
				$this->getItemParams($row, $this->params) + $this->parameters
			));

		if (!empty($this->dataAttributes)) {
			foreach ($this->dataAttributes as $key => $attrValue) {
				$a->data($key, $attrValue);
			}
		}

		if ($this->openInNewTab) {
			$a->addAttributes(['target' => '_blank']);
		}

		if ($this->title) {
			$a->title($this->title);
		}

		if ($this->class) {
			$a->class($this->class);
		}

		$element = $a;

		if ($this->icon) {
			$a->addHtml(Html::el('span')->class(DataGrid::$iconPrefix . $this->icon));

			if (strlen($value)) {
				$a->addHtml('&nbsp;');
			}
		}

		if ($this->isTemplateEscaped()) {
			$a->addText($value);
		} else {
			$a->addHtml($value);
		}

		return $element;
	}


	public function addParameters(array $parameters): self
	{
		$this->parameters = $parameters;

		return $this;
	}


	public function setIcon(?string $icon = null): self
	{
		$this->icon = $icon;

		return $this;
	}


	/**
	 * @param mixed $value
	 */
	public function setDataAttribute(string $key, $value): self
	{
		$this->dataAttributes[$key] = $value;

		return $this;
	}


	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}


	public function getTitle(): ?string
	{
		return $this->title;
	}


	public function setClass(string $class): self
	{
		$this->class = $class;

		return $this;
	}


	public function getClass(): ?string
	{
		return $this->class;
	}


	public function isOpenInNewTab(): bool
	{
		return $this->openInNewTab;
	}


	public function setOpenInNewTab(bool $openInNewTab = true): self
	{
		$this->openInNewTab = $openInNewTab;

		return $this;
	}

}

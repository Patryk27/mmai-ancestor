<?php

namespace App\Core\Table;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use LogicException;

class Renderer
{
    /** @var ViewFactory */
    private $viewFactory;

    /** @var View[] */
    private $columns;

    public function __construct(ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * Adds a column to the renderer.
     *
     * Example:
     *  $renderer->addColumn('user', 'backend.something.somewhere.userName')
     *
     * @see addColumns()
     *
     * @param string $columnName
     * @param string $viewName
     * @return void
     */
    public function addColumn(string $columnName, string $viewName): void
    {
        if (array_has($this->columns, $columnName)) {
            throw new LogicException(sprintf(
                'Column [%s] has been already added into the renderer.', $columnName
            ));
        }

        $this->columns[$columnName] = $this->viewFactory->make($viewName);
    }

    /**
     * Adds many columns to the renderer.
     *
     * Example:
     *  $renderer->addColumns([
     *    'user' => 'backend.something.somewhere.user',
     *    'language' => 'backend.something.somewhere.language',
     *  ]);
     *
     * @see addColumn()
     *
     * @param array $columns
     * @return void
     */
    public function addColumns(array $columns): void
    {
        foreach ($columns as $columnName => $viewName) {
            $this->addColumn($columnName, $viewName);
        }
    }

    /**
     * Renders given rows basing on previously set columns.
     *
     * @param Collection $rows
     * @return Collection
     */
    public function render(Collection $rows): Collection
    {
        return $rows->map(function ($row): array {
            $result = [];

            foreach ($this->columns as $columnName => $columnView) {
                $result[] = $columnView
                    ->with('row', $row)
                    ->render();
            }

            return $result;
        });
    }
}

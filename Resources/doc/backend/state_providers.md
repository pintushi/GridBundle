State Providers
===============

Overview
--------

State providers must implement interface `Pintushi\Bundle\GridBundle\Provider\State\DatagridStateProviderInterface`.
A state provider must return an array representing the state of a datagrid component. A state is represented by an array which contains request- and user-specific data about the current datagrid component settings (state). For example, for columns it can contain information for each column about whether it or its order (weight) are renderable (visible).

Initially, due to the specifics of datagrid frontend implementation, a datagrid state has been introduced for the frontend to adjust the datagrid view according to user preferences, e.g. show only specific columns in a specific order.

Later, a datagrid state started to be used on backend, e.g. for sorters and adjustment of datasource queries.

State providers return the state which is actual at the moment of a call. This means that if it is called in the datagrid extension`s `processConfigs()` method, it will return the state for that particular moment only. In other extensions and listeners, states can differ if datagrid configuration has been changed.

OroDatagridBundle provides 2 datagrid state providers out-of-the-box:

- `pintushi_grid.provider.state.columns` (`Pintushi\Bundle\GridBundle\Provider\State\ColumnsStateProvider`)
- `pintushi_grid.provider.state.sorters` (`Pintushi\Bundle\GridBundle\Provider\State\SortersStateProvider`)

ColumnsStateProvider
--------------------

ColumnsStateProvider provides request- and user-specific datagrid state for the columns component.

It tries to fetch state from datagrid parameters, then falls back to state from current datagrid view,
then from default datagrid view, then to datagrid columns configuration.

State is represented by an array with column names as key and array with the following keys as values:

- `renderable`: boolean, whether a column must be displayed on frontend;
- `order`: int, column order (weight).

Example:

``` php
$columnsStateProvider = $this->container->get('pintushi_grid.provider.state.columns');
$state = $columnsStateProvider->getState($datagridConfiguration, $datagridParameters);
var_export($state);
// Will output
//[
//    'sampleColumn1' => ['renderable' => true, 'order' => 0],
//    'sampleColumn2' => ['renderable' => true, 'order' => 1],
//]
```

SortersStateProvider
--------------------

SortersStateProvider provides request- and user-specific datagrid state for the sorters component.

It tries to fetch state from datagrid parameters, then falls back to state from current datagrid view, then from default
datagrid view, then to datagrid columns configuration.

State is represented by an array with sorters names as key and order direction as a value.

Example:

``` php
$sortersStateProvider = $this->container->get('pintushi_grid.provider.state.sorters');
$state = $sortersStateProvider->getState($datagridConfiguration, $datagridParameters);
var_export($state);
// Will output
//[
//    'sampleColumn1' => 'ASC',
//]
```

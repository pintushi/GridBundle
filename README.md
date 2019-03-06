# PintushiGridBundle

Even though this bundle is a variation of OroGridBundle, I made lots of changes for our cases, treat OroGridBundle as a start point. I want to use OroGridBundle archetecture so we can add new features to grid easily, also simplify it for API use.

# Features

1. Grid state
2. Collumns, sorters, filters works out of box.
3. Integrate "pagerfanta/pagerfanta"

Grid state includes collumns, sorters, filters and pagination settings, these are metadatas, before the client try to show a list, it first makes a request to get these metadata, then fetch actual data by another request. filters are Symfony forms which will be hydrated to client by "limenius/liform-bundle".

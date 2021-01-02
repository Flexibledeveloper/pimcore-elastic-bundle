# PimcoreElasticBundle

## Description
This bundle provides a way to connect pimcore to elasticsearch via [Ruflin/Elastica](https://github.com/ruflin/elastica).
It provides some services which can be used by Pimcore to help you concentrate on content not how to connect to elastic.

## Basic concept
The bundle provides symfony services to e.g. create an index or populate documents with data.

You as implementing developer provide the triggers, e.g. EventSubscriber, and the normalized data objects to populate with. 

This ensures your index matches your documents and you have the freedom to easily store the documents as you like


## Setup
Include this bundle into your codebase.

- Rename the `elastic.dist.yml` to `elastic.yml` and update its content to your needs
- Create a normalizer for the data you want to use in elasticsearch
- Create a EventSubscriber to hook into the event you want the index to be updated at

#!/bin/bash

php vendor/bin/doctrine orm:schema-tool:update --force
php vendor/bin/doctrine orm:generate-proxies

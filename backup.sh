#!/bin/bash

rm -rf ./storage/log/*
rm -rf ./storage/debugbar/*
php artisan backup:run

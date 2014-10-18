<?php

return [
        "list" => [
                [
                        "name"        => "Products",
                        "route"       => "products",
                        "link"        => URL::route('products.lists'),
                        "permissions" => ["_admin"]
                ],
                [
                        "name"        => "Categories",
                        "route"       => "category",
                        "link"        => URL::route('category.lists'),
                        "permissions" => ["_admin"]
                ]
        ]
];
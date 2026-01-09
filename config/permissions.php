<?php

return [
    'permissions' => [
        'Admin',
        'Supervisor',
        'Sales Clerk',
        'Warehouse Keeper',
        'Return and Exchange Clerk',
        'Inventory Clerk'
    ]
];

// make all prural becuase its confusing except for button




// return [
//     'Admin/Owner' => [
//         'manage admin panels',
//         'manage products',
//         'manage sales/customers',
        
     
//         'manage purchases/suppliers',
        
//         'edit permission',
//         'delete permission',
//         'download permission',
//         'manage add products',
//              'manage orders/unsuccessful',

//         'manage returns',
//         'manage product defects',
//         'manage total amounts'

//     ],

//     'Supervisor' =>  [
//         'edit permission',
//         'delete permission',
//         'download permission',

//         'manage product defects',
//         'manage total amounts',

//         'manage products',

//         'manage admin panels',
//         'manage purchases/suppliers',
//         'manage sales/customers',
//               'manage orders/unsuccessful',

//         'manage returns',

//         'manage add products',

        

//     ],

//     'Sales Clerk'  =>  [
//         'manage sales/customers',

//     ],


//     'Inventory Clerk' =>  [
//         'manage add products',

//     ],

//     'Warehouse Keeper' =>  [
//         'manage orders/unsuccessful',


//     ],

//     'Return and Exchange Clerk'  => [
//         'manage returns',


//     ],

// ];

// this is the prep and final preparetion for permissions
// prep=['user','dashboard','unit','sale','role','customer','supplier','brand',
// 'category','product','purchase','order','new arrival','return','unsuccesfull transaction',
// 'approval','product defect','notes',];
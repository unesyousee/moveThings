<?php
/**
 * Created by PhpStorm.
 * User: nobaar
 * Date: 7/11/19
 * Time: 3:38 PM
 */
return [
    "types" => [
        0 => 'کد تخفیف',
        1 => 'کمیسیون نوبار',
        2 => 'شارز کیف پول',
        3 => 'کسر از کیف پول',
        4 => 'مبلغ سفارش',
        5 => 'جریمه تاخیر',
        6 => 'اصلاحیه',
        7 => 'شارژ کیف پول', // پرداخت بانکی راننده
        8 => 'دریافت نقدی از مشتری',
        9 => 'پرداخت نقدی',
        10 => 'واریز به کارت راننده',
        11 => 'پرداخت اعتباری مشتری',
        12 => 'جریمه امتیاز',
        13 => 'پاداش راننده',
        14 => 'جریمه خسارت بار',
    ],
    "adaptor" => [
        "debit" => "برداشت",
//        "debit" => "بدهی به نوبار بابت سفارش",
        "credit" => "واریز",
//        "credit" => "بستانکاری از شرکت بابت سفارش",
    ],
];

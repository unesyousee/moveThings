<?php

namespace App\Http\Controllers;

use App\Discount;
use App\DiscountUsage;
use App\Order;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class TransactionSaver
 * @package App\Http\Controllers
 */

class TransactionSaver
{
    public $driver_user_id;
    public $order_id;
    public $third_user_id;
    public $user_id;
    public $oprator_id;

    private $transaction_type = [
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
    ];

    public function __construct()
    {
        $this->oprator_id = Auth::user()->id ?? null;
    }

    public function nobaarGetCashDriver()
    {
        list($discount_usage, $discountAmount) = $this->discountChecker();
        $order = Order::find($this->order_id);
        $commission = $this->driverCommission($order);
        if (!is_null($discountAmount)) {
            $data = [
                [
                    'amount' => abs($discountAmount),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[0],
                    'transaction_type' => 0,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => abs($order->price),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[8],
                    'transaction_type' => 8,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => (-abs($commission)),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[1],
                    'transaction_type' => 1,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]
            ];
            $discount_usage->status = 1;
            $discount_usage->save();
        } else {

            $data = [
                [
                    'amount' => abs($order->price),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[8],
                    'transaction_type' => 8,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => (-abs($commission)),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[1],
                    'transaction_type' => 1,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]
            ];
        }
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function ThirdCommission()
    {
        $order = Order::find($this->order_id);
        $third = $order->thirdparty()->first();
        $data = [
            [
                'amount' => abs(($third->commission * $order->price) / 100),
                'order_id' => $order->id,
                'user_id' => $third->user->id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[1],
                'transaction_type' => 1,
                'operator_id' => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function driverGetCash()
    {
        $order = Order::find($this->order_id);
        $commission = $this->driverCommission($order);
        list($discount_usage, $discountAmount) = $this->discountChecker();
        if (!is_null($discountAmount)) {
            $data = [
                [
                    'amount' => abs($discountAmount),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'discount_usage_id' => $discount_usage->id,
                    'online' => '0',
                    'description' => $this->transaction_type[0],
                    'transaction_type' => 0,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => -abs($commission),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'discount_usage_id' => null,
                    'online' => '0',
                    'description' => $this->transaction_type[1],
                    'transaction_type' => 1,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]
            ];
        } else {
            $data = [
                [
                    'amount' => (-abs($commission)),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[1],
                    'transaction_type' => 1,
                    'operator_id' => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]
            ];
        }
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function userCashOrder()
    {
        $order = Order::find($this->order_id);
        $discount_usage_obj = $order->discountUsages()->where('status', 1)->first();
        if ($discount_usage_obj) {
            $discount_amount = $this->getUserDiscountAmount($discount_usage_obj, $order);
            $data = [
                [
                    'amount' => abs($discount_amount),
                    'discount_usage_id' => $discount_usage_obj->id,
                    'order_id' => $this->order_id,
                    'user_id' => $this->user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[0],
                    'transaction_type' => 0,
                    'operator_id' => $this->oprator_id ?? '',
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
            ];
            $t = new Transaction();
            $t->insert($data);
        }
//dd($data);
        return $this;
    }

    public function driverOnlineOrder()
    {
        $t = new Transaction();
        $order = Order::find($this->order_id);
        $commission = $this->driverCommission($order);
        list($discount_usage, $discountAmount) = $this->discountChecker();
        if (!is_null($discountAmount)) {
            $data = [
                [
                    'amount' => abs($discountAmount),
                    'order_id' => $this->order_id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '1',
                    'description' => $this->transaction_type[0],
                    'transaction_type' => 0,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => abs($order->price - $discountAmount),
                    'order_id' => $this->order_id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '1',
                    'description' => $this->transaction_type[11],
                    'transaction_type' => 11,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => -abs(($commission)),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[1],
                    'transaction_type' => 1,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]
            ];
        } else {
            $data = [
                [
                    'amount' => abs($order->price),
                    'order_id' => $this->order_id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '1',
                    'description' => $this->transaction_type[11],
                    'transaction_type' => 11,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => -abs(($commission)),
                    'order_id' => $order->id,
                    'user_id' => $this->driver_user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[1],
                    'transaction_type' => 1,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]
            ];
        }
        $t->insert($data);
        return $this;
    }

    public function userOnlineOrder()
    {
        $order = Order::find($this->order_id);
        $discount_usage_obj = $order->discountUsages()->where('status', 1)->first();
        if ($discount_usage_obj) {
            $discount_amount = $this->getUserDiscountAmount($discount_usage_obj, $order);
            $data = [
                [
                    'amount' => abs($discount_amount),
                    'discount_usage_id' => $discount_usage_obj->id,
                    'order_id' => $this->order_id,
                    'user_id' => $this->user_id,
                    'status' => '1',
                    'online' => '1',
                    'description' => $this->transaction_type[0],
                    'transaction_type' => 0,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
                [
                    'amount' => -abs($order->price),
                    'order_id' => $this->order_id,
                    'discount_usage_id' => null,
                    'user_id' => $this->user_id,
                    'status' => '1',
                    'online' => '1',
                    'description' => $this->transaction_type[3],
                    'transaction_type' => 3,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]
            ];
        } else {
            $data =
                [
                    'amount' => -abs($order->price),
                    'order_id' => $this->order_id,
                    'user_id' => $this->user_id,
                    'status' => '1',
                    'online' => '1',
                    'description' => $this->transaction_type[3],
                    'transaction_type' => 3,
                    "operator_id" => $this->oprator_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ];
        }
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function thirdCashUser()
    {
        $order = Order::find($this->order_id);
        $data = [
            [
                'amount' => -abs($order->price),
                'order_id' => $this->order_id,
                'user_id' => $this->user_id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[4],
                'transaction_type' => 4,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                'amount' => abs(($order->price)),
                'order_id' => $this->order_id,
                'user_id' => $this->user_id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[4],
                'transaction_type' => 4,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function thirdCashThird()
    {
        $order = Order::find($this->order_id);
        $commission = User::find($this->third_user_id)->thirdparty()->first()->commission;
        $commission = ($order->price * $commission) / 100;
        $data = [
            [
                'amount' => -abs($order->price),
                'order_id' => $this->order_id,
                'user_id' => $this->third_user_id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[4],
                'transaction_type' => 4,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                'amount' => abs(($order->price)),
                'order_id' => $this->order_id,
                'user_id' => $this->third_user_id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[4],
                'transaction_type' => 4,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                'amount' => abs(($commission)),
                'order_id' => $this->order_id,
                'user_id' => $this->third_user_id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[1],
                'transaction_type' => 1,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function thirdOnlineUser()
    {
        $order = Order::find($this->order_id);
        $data = [
            [
                'amount' => -abs($order->price),
                'order_id' => $this->order_id,
                'user_id' => $this->user_id,
                'status' => '1',
                'online' => '1',
                'description' => $this->transaction_type[7],
                'transaction_type' => 7,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                'amount' => abs(($order->price)),
                'order_id' => $this->order_id,
                'user_id' => $this->user_id,
                'status' => '1',
                'online' => '1',
                'description' => $this->transaction_type[4],
                'transaction_type' => 4,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function thirdOnlineThird()
    {
        $order = Order::find($this->order_id);
        $commission = User::find($this->third_user_id)->thirdparty()->first()->commission;
        $commission = ($order->price * $commission) / 100;
        $data = [
            [
                'amount' => -abs($order->price),
                'order_id' => $this->order_id,
                'user_id' => $this->third_user_id,
                'status' => '1',
                'online' => '1',
                'description' => $this->transaction_type[4],
                'transaction_type' => 4,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                'amount' => abs(($commission)),
                'order_id' => $this->order_id,
                'user_id' => $this->third_user_id,
                'status' => '1',
                'online' => '1',
                'description' => $this->transaction_type[1],
                'transaction_type' => 1,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function thirdOnlineDriver()
    {
        $order = Order::find($this->order_id);
        $commission = $this->driverCommission($order);
        $data = [
            [
                'amount' => -abs(($commission)),
                'order_id' => $this->order_id,
                'user_id' => $this->driver_user_id,
                'status' => '1',
                'online' => '1',
                'description' => $this->transaction_type[1],
                'transaction_type' => 1,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                'amount' => abs(($order->price)),
                'order_id' => $order->id,
                'user_id' => $this->driver_user_id,
                'status' => '1',
                'online' => '1',
                'description' => $this->transaction_type[2],
                'transaction_type' => 2,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]

        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function cashOut($amount, $user_id)
    {
        $data = [
            [
                'amount' => $amount,
                'user_id' => $user_id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[10],
                'transaction_type' => 10,
                "operator_id" => $this->oprator_id,
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function chargeUserWallet($amount, $type)
    {
        $t = new Transaction();
        $t->insert([
            'amount' => $amount,
            'user_id' => $this->user_id,
            'status' => '1',
            'online' => '0',
            'description' => $this->transaction_type[$type],
            'transaction_type' => $type,
            "operator_id" => $this->oprator_id,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        ]);
    }

    private function driverCommission($order)
    {
        $commission = (int)User::find($this->driver_user_id)->carrierUsers()->first()->commission;
        $commission = ($order->price * $commission) / 100;
        return $commission;
    }

    private function getUserDiscountAmount($discount_usage_obj, $order)
    {
        $discount = $discount_usage_obj->discount->amount;
        if ($discount_usage_obj->discount->type == 1) {
            $discount_amount = ($order->price * $discount) / 100;
        } else {
            $discount_amount = $discount;
        }
        return $discount_amount;
    }

    public function driverPenalty(int $amount)
    {
        $data = [
            [
                'amount' => -abs(($amount)),
                'order_id' => $this->order_id,
                'user_id' => $this->driver_user_id,
                'status' => '1',
                'online' => '1',
                'description' => $this->transaction_type[5],
                'transaction_type' => 5,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function userGetDiscount()
    {
        list($discount_usage, $discountAmount) = $this->discountChecker();
        if (!is_null($discountAmount)) {
            $data = [
                [
                    'amount' => abs($discountAmount),
                    'discount_usage_id' => $discount_usage->id,
                    'order_id' => $this->order_id,
                    'user_id' => $this->user_id,
                    'status' => '1',
                    'online' => '0',
                    'description' => $this->transaction_type[0],
                    'transaction_type' => 0,
                    'operator_id' => $this->oprator_id ?? '',
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ],
            ];
            $t = new Transaction();
            $t->insert($data);
            $discount_usage->status = 1;
            $discount_usage->save();
        }
    }

    /**
     * @return array
     */
    private function discountChecker()
    {
        $order = Order::find($this->order_id);
        $amount = null;
        $discount_usage = DiscountUsage::where('order_id', $this->order_id)->where('status', 0)->first();
        $new_price = $order->price;
        if ($discount_usage != null) {
            if ($discount_usage->discount_id != null) {
                $discount = Discount::find($discount_usage->discount_id);
                if ($discount->type == 0) {
                    $amount = $discount->amount;
                } else {
                    $amount = ($new_price * $discount->amount / 100);
                }

            } else {
                $discount = Price::where('title', 'کد معرف')->first();
                if ($discount->type == 0) {
                    $amount = $discount->amount;
                } else {
                    $amount = ($new_price * $discount->amount / 100);
                }

            }
        }
        return array($discount_usage, $amount);
    }
    public function thirdDiscount($amount)
    {
        $order = Order::find($this->order_id);
        $third = $order->thirdparty()->first();
        $data = [
            [
                'amount' => -abs($amount),
                'order_id' => $order->id,
                'user_id' => $third->user->id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[0],
                'transaction_type' => 0,
                'operator_id' => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }


    public function ratingPenalty(int $amount)
    {
        $data = [
            [
                'amount' => -abs(($amount)),
                'user_id' => $this->driver_user_id,
                'status' => '1',
                'online' => '0',
                'description' => $this->transaction_type[12],
                'transaction_type' => 12,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }

    public function driverGift(int $amount)
    {
        $data = [
            [
                'amount' => abs(($amount)),
                'user_id' => $this->driver_user_id,
                'status' => '1',
                'order_id' => $this->order_id,
                'online' => '0',
                'description' => $this->transaction_type[13],
                'transaction_type' => 13,
                "operator_id" => $this->oprator_id,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]
        ];
        $t = new Transaction();
        $t->insert($data);
        return $this;
    }
}

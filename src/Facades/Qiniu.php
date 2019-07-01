<?php

/*
 * This file is part of the laravuel/qiniu.
 *
 * (c) laravuel <45761113@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Laravuel\Qiniu\Facades;
use Illuminate\Support\Facades\Facade;

class Qiniu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'qiniu';
    }
}
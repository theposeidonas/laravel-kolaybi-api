<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum DocumentScenario: string
{
    use HasValues;

    case TEMELFATURA = 'TEMELFATURA';
    case TICARIFATURA = 'TICARIFATURA';
    case IHRACAT = 'IHRACAT';
    case YOLCUBERABERFATURA = 'YOLCUBERABERFATURA';
    case KAMU = 'KAMU';
    case HKS = 'HKS';
    case ENERJI = 'ENERJI';
    case ILAC_TIBBICIHAZ = 'ILAC_TIBBICIHAZ';
    case YATIRIMTESVIK = 'YATIRIMTESVIK';
    case EARSIVFATURA = 'EARSIVFATURA';
    case TEMELIRSALIYE = 'TEMELIRSALIYE';
    case SERBESTMESLEKMAKBUZU = 'SERBESTMESLEKMAKBUZU';
}

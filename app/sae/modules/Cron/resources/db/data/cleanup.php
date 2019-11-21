<?php
// Clean-up old cron jobs!
Nwicode_Feature::removeCronjob('statistics');
Nwicode_Feature::removeCronjob('androidtools');
Nwicode_Feature::removeCronjob('cachebuilder');
Nwicode_Feature::removeCronjob('quotawatcher');
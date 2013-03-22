<?php

/**
 * This file can be included to include all dependent class files.
 * We are using the profiler very early in T3's bootstrap, so AutoLoading
 * won't be available!
 */

require('Classes/Profiler.php');
require('Classes/Domain/Model/EmptyProfilingRun.php');
require('Classes/Domain/Model/ProfilingRun.php');
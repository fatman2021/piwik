<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Referrers\Widgets\Subcategories;

use Piwik\Widget\Subcategory;

class SearchEnginesSubcategory extends Subcategory
{
    protected $categoryId = 'Referrers_Referrers';
    protected $id = 'Referrers_SubmenuSearchEngines';
    protected $order = 10;

}

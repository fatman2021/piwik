<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Actions\Tracker;

use Piwik\Tracker\Action;
use Piwik\Tracker\Request;
use Piwik\Tracker\RequestProcessor;
use Piwik\Tracker\Visit\VisitProperties;
use Piwik\Tracker\Visitor;

/**
 * TODO
 *
 * TODO: document request metadata here (ie, 'actions')
 */
class ActionsRequestProcessor extends RequestProcessor
{
    public function processRequestParams(VisitProperties $visitProperties, Request $request)
    {
        // normal page view, potentially triggering a URL matching goal
        $action = Action::factory($request);
        $action->writeDebugInfo();

        $visitProperties->setRequestMetadata('Actions', 'action', $action);
    }

    public function manipulateVisitProperties(VisitProperties $visitProperties, Request $request)
    {
        /** @var Action $action */
        $action = $visitProperties->getRequestMetadata('Actions', 'action');

        if (!empty($action)) { // other plugins can unset the action if they want
            $action->loadIdsFromLogActionTable();
        }

        // save the exit actions of the last action in this visit as the referrer actions for the action being tracked.
        // when the visit is updated, these columns will be changed, so we have to do this before processRequest
        $visitProperties->setRequestMetadata('Actions', 'idReferrerActionUrl', @$visitProperties->visitorInfo['visit_exit_idaction_url']);
        $visitProperties->setRequestMetadata('Actions', 'idReferrerActionName', @$visitProperties->visitorInfo['visit_exit_idaction_name']);
    }

    public function processRequest(Visitor $visitor, VisitProperties $visitProperties)
    {
        /** @var Action $action */
        $action = $visitProperties->getRequestMetadata('Actions', 'action');

        if ($action !== null
            && !$visitProperties->getRequestMetadata('CoreHome', 'visitorNotFoundInDb')
        ) {
            $idReferrerActionUrl = 0;
            $idReferrerActionName = 0;

            if (!$visitProperties->getRequestMetadata('CoreHome', 'isNewVisit')) {
                $idReferrerActionUrl = $visitProperties->getRequestMetadata('Actions', 'idReferrerActionUrl');
                $idReferrerActionName = $visitProperties->getRequestMetadata('Actions', 'idReferrerActionName');
            }

            $action->record($visitor, $idReferrerActionUrl, $idReferrerActionName);
        }
    }
}

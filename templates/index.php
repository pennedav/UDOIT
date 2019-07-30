<?php
/**
*	Copyright (C) 2014 University of Central Florida, created by Jacob Bates, Eric Colon, Fenel Joseph, and Emily Sachs.
*
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*	Primary Author Contact:  Jacob Bates <jacob.bates@ucf.edu>
*/

use Gettext\Translator;
use Gettext\Translations;
global $ui_locale;

$translator = new Translator();
$translations = Translations::fromPoFile(__DIR__."/../locales/{$ui_locale}.po");
$translator->loadTranslations($translations);
$translator->register();

$settings = [
	'footer_scripts' => [
		"//code.jquery.com/jquery-2.1.1.min.js",
		"//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js",
		"assets/js/vendor/jscolor/jscolor.js",
		"assets/js/resize.js?v=".UDOIT_VERSION,
		"assets/js/default.js?v=".UDOIT_VERSION,
		"assets/js/contrast.js",
	]
];

$this->layout('template', $settings);

?>
<ul class="nav nav-tabs nav-justified" role="tablist">
	<li role="presentation" class="active"><a href="#scanner" role="tab" data-toggle="tab"><?= __('scanTab'); ?></a></li>
	<li role="presentation"><a href="#cached" role="tab" data-toggle="tab"><?= __('viewOldTab'); ?></a></li>
</ul>
<main id="contentWrapper" role="main">
	<div class="tab-content">
		<div class="tab-pane active" id="scanner" role="tabpanel">
			<div class="panel panel-default">
				<div class="panel-body">
					<h2><?= __('welcomeTitle'); ?></h2>

					<p><?= $welcome_message; ?></p>

					<p><?= $disclaimer_message; ?></p>

					<p class="no-margin"><a href="#udoitInfo" class="btn btn-sm btn-default no-print" data-toggle="modal" data-target="#udoitInfo"><?= __('whatLookFor'); ?></a></p>
				</div>
			</div>
			<form class="form-horizontal no-print" id="udoitForm" action="#" role="form">
				<input type="hidden" name="main_action" value="udoit">
				<input type="hidden" name="base_url" value="<?= $this->escape($base_url); ?>/">
				<input type="hidden" name="session_course_id" value="<?= $this->escape($launch_params['custom_canvas_course_id']); ?>">
				<input type="hidden" name="session_context_label" value="<?= $this->escape($launch_params['context_label']); ?>">
				<input type="hidden" name="session_context_title" value="<?= $this->escape($launch_params['context_title']); ?>">

				<div class="form-group">
					<span class="col-sm-2 control-label"><strong><?= __('selectContent'); ?></strong></span>

					<div class="col-sm-10">
						<div class="checkbox">
							<label><input id="allContent" type="checkbox" value="all" id="allContent" class="content" name="content[]" checked> <?= __('all'); ?></label>
						</div>

						<hr />

						<div class="checkbox">
							<label><input id="courseAnnouncements" type="checkbox" value="announcements" class="content" name="content[]" checked> <?= __('announcements'); ?></label>
						</div>

						<div class="checkbox">
							<label><input id="courseAssignments" type="checkbox" value="assignments" class="content" name="content[]" checked> <?= __('assignments'); ?></label>
						</div>

						<div class="checkbox">
							<label><input id="courseDiscussions" type="checkbox" value="discussions" class="content" class="content" name="content[]" checked> <?= __('discussions'); ?></label>
						</div>

						<div class="checkbox">
							<label><input id="courseFiles" type="checkbox" value="files" class="content" name="content[]" checked> <?= __('files'); ?></label>
						</div>

						<div class="checkbox">
							<label><input id="coursePages" type="checkbox" value="pages" class="content" name="content[]" checked> <?= __('pages'); ?></label>
						</div>

						<div class="checkbox">
							<label><input id="courseSyllabus" type="checkbox" value="syllabus" class="content" name="content[]" checked> <?= __('syllabus'); ?></label>
						</div>

						<div class="checkbox">
							<label><input id="moduleUrls" type="checkbox" value="module_urls" class="content" name="content[]" checked> <?= __('moduleUrls'); ?></label>
						</div>
					</div>
				</div>

				<hr />

				<div id="waitMsg" class="alert alert-warning" style="display: none;">
					<p><span class="glyphicon glyphicon-warning-sign"></span> <?= __('stayOnPage'); ?></p>
				</div>

				<button type="submit" name="course_submit" class="btn btn-block btn-lg btn-success submit"><?= __('scanThisCourse'); ?></button>

				<div class="alert alert-danger no-margin margin-top" id="failMsg" style="display: none;">
					<span class="glyphicon glyphicon-exclamation-sign"></span> <span class="msg"><?= __('failedToScan'); ?></span><span class="custom-msg"></span>
				</div>
			</form>
		</div>
		<div class="tab-pane" id="cached" role="tabpanel">

		</div>
	</div>

	<div class="modal fade" id="udoitInfo" tabindex="-1" role="dialog" aria-labelledby="udoitModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

					<h4 class="modal-title" id="udoitModalLabel"><?= __('whatLookForTitle'); ?></h4>
				</div>
				<div class="modal-body">
					<?= $this->fetch('partials/look_for_modal_list', ['style_classes' => 'errorItem panel panel-danger', 'title' => 'Errors', 'tests' => $udoit_tests['severe']]); ?>

					<?= $this->fetch('partials/look_for_modal_list', ['style_classes' => 'panel panel-info no-margin', 'title' => 'Suggestions', 'tests' => $udoit_tests['suggestion']]); ?>

				</div>
			</div>
		</div>
	</div>
</main>

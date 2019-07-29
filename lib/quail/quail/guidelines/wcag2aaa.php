<?php
/**
*    QUAIL - QUAIL Accessibility Information Library
*    Copyright (C) 2009 Kevin Miller
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*	@author Kevin Miller <kemiller@csumb.edu>
*/


/** \addtogroup guidelines */
/*@{*/

/**
*	WCAG 2.0 Level AAA Guideline
*/
class Wcag2aaaGuideline extends quailGuideline
{
	/**
	*	@var array An array of test class names which will be called for this guideline
	*/
	var $tests = [
		'aLinksToMultiMediaRequireTranscript',
		'aLinksToSoundFilesNeedTranscripts',
		'aMustContainText',
		'aSuspiciousLinkText',
		'basefontIsNotUsed',
		'blinkIsNotUsed',
		'contentTooLong',
		'cssTextHasContrast',
		'cssTextStyleEmphasize',
		'documentReadingDirection',
		'embedHasAssociatedNoEmbed',
		'fontIsNotUsed',
		'headersHaveText',
		'imgAltIsDifferent',
		'imgAltIsTooLong',
		'imgAltNotEmptyInAnchor',
		'imgAltNotPlaceHolder',
		'imgGifNoFlicker',
		'imgHasAlt',
		'imgHasAltDeco',
		'imgHasLongDesc',
		'inputImageNotDecorative',
		'marqueeIsNotUsed',
		'noHeadings',
		'objectInterfaceIsAccessible',
		'objectMustContainText',
		'objectShouldHaveLongDescription',
		'objectTagDetected',
		'pNotUsedAsHeader',
		'preShouldNotBeUsedForTabularLayout',
		'tableDataShouldHaveTh',
		'tableThShouldHaveScope',
		'videoEmbedChecked',
		'videoProvidesCaptions',
		'videosEmbeddedOrLinkedNeedCaptions',
	];
}
/*@}*/

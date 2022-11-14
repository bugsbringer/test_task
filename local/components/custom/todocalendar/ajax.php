<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class TodoCalendarAjax extends \Bitrix\Main\Engine\Controller {
	protected const notesFile = "notes.json";

	public function addNoteAction($note, $date) {
		$filePath = dirname($this->getFilePath())."/".self::notesFile;

		$notes = file_exists($filePath) 
			? json_decode(file_get_contents($filePath), true)
			: [];

		$notes[$date][] = $note;

		file_put_contents($filePath, json_encode($notes));

		return ['dateNotes' => $notes[$date], 'date' => $date];
	}
}

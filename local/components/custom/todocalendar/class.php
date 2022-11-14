<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class TodoCalendarComponent extends \CBitrixComponent {

    protected const notesFile = "notes.json";
    
    public function executeComponent() {
        $uri = $this->request->getRequestedPage();

        $timestamp = (int)$this->request->get('timestamp') ?: strtotime("now");
        $this->arResult['uri'] = $uri;

        $this->arResult['prev'] = getdate(strtotime("first day of previous month 00:00:00", $timestamp));
        $this->arResult['current'] = getdate(strtotime("first day of this month 00:00:00", $timestamp));
        $this->arResult['next'] = getdate(strtotime("first day of next month 00:00:00", $timestamp));

        $this->arResult['days'] = (int)date('d', strtotime("last day of this month", $timestamp));
        $this->arResult['month_start_weekday'] = $this->arResult['current']['wday'] ? $this->arResult['current']['wday'] - 1 : 6;
        $this->arResult['notes'] = $this->getNotes();

        $this->IncludeComponentTemplate();
    }

    private function getNotes() {
        $filePath = $this->getNotesFilePath();
        return file_exists($filePath) 
            ? json_decode(file_get_contents($filePath), true)
            : [];
    }

    private function getNotesFilePath() {
        return "{$_SERVER['DOCUMENT_ROOT']}{$this->getPath()}/".self::notesFile;
    }
}
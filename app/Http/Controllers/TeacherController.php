<?php

namespace App\Http\Controllers;

use App\Services\TeacherSpreadsheetService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherController extends Controller
{
    public function template(TeacherSpreadsheetService $spreadsheets): StreamedResponse
    {
        return $spreadsheets->template();
    }

    public function export(TeacherSpreadsheetService $spreadsheets): StreamedResponse
    {
        return $spreadsheets->export();
    }
}

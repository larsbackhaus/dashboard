<?php
namespace App\Http\Controllers;

use App\Http\Requests\Api\ExcelFileStore;
use App\Http\Requests\Api\UpdateChartPos;
use App\Http\Requests\Api\UpdateChartVisible;
use App\Http\Requests\Api\UpdateChartMode;
use App\Http\Resources\ChartData;
use App\Http\Resources\ChartDataCollection;
use Illuminate\Http\Request;
use App\Models\ExcelFile;
use App\Models\Chart;
use Illuminate\Support\Facades\DB;

/**
 * Class ExcelFilesController
 *
 * @package App\Http\Controllers
 */
class ChartController extends Controller
{
    public function store(ExcelFileStore $request)
    {
        $userId = request('user_id');
        $file = $request->file('upload_file');

        $dir = '/csv_uploads' . date('/Y/m/');
        $originalName = $file->getClientOriginalName();
        $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path() . $dir, $name);

        $dbrequest = array(
            'userid' => $userId,
            'file' => $dir . $name,
            'file_name' => $originalName
        );

        $fid = ExcelFile::insertGetId($dbrequest);

        $dbrequest = array(
            'data_id' => $fid,
            'visible' => 1,
        );

        Chart::insert($dbrequest);

        return $fid;
    }

    public function updateChartVisible(UpdateChartVisible $request, $id) {
        Chart::where('data_id', $id)
            ->update(['visible' => request('visible')]);

        return 'Success chart visible update';
    }

    public function updateChartPos(UpdateChartPos $request, $id) {
        $dbrequest = array(
            'ch_left' => request('ch_left'),
            'ch_top' => request('ch_top'),
            'ch_width' => request('ch_width'),
            'ch_height' => request('ch_height'),
        );

        Chart::where('data_id', $id)
            ->update($dbrequest);

        return 'Success chart positions update';
    }

    public function updateChartMode(UpdateChartMode $request, $id) {
        Chart::where('data_id', $id)
            ->update(['mode' => request('mode')]);

        return 'Success chart mode update';
    }

    public function getChartDataById(Request $request) {
        $chartId = request('id');

        $chart = DB::table('user_uploads_data')
            ->where('user_uploads_data.id', $chartId)
            ->join('chart', 'user_uploads_data.id', '=', 'chart.data_id')
            ->join('chart_mode', 'chart.mode', '=', 'chart_mode.id')
            ->select('user_uploads_data.id', 'file', 'file_name', 'ch_left', 'ch_top', 'ch_width', 'ch_height', 'visible', 'chart_mode.name as mode')
            ->get();

        return new ChartData($chart);
    }

    public function getAllForUser(Request $request)
    {
        $userId = request('user_id');

        $allCharts = DB::table('user_uploads_data')
            ->where('userid', $userId)
            ->join('chart', 'user_uploads_data.id', '=', 'chart.data_id')
            ->join('chart_mode', 'chart.mode', '=', 'chart_mode.id')
            ->select('user_uploads_data.id', 'file', 'file_name', 'ch_left', 'ch_top', 'ch_width', 'ch_height', 'visible', 'chart_mode.name as mode')
            ->get();

        return new ChartDataCollection($allCharts);
    }
}

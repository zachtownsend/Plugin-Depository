<?php

namespace App\Http\Controllers;

use App\Plugin;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Zip;

class PluginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $plugin = $request->file('plugin');
        $zip = Zip::open($plugin->getRealPath());
        $zip_files = $zip->listFiles();
        $storage_path = storage_path('app/process');
        $root_folder = $zip_files[0];
        $file_path = $storage_path . '/' . $root_folder;

        $zip->extract($storage_path);

        $files = scandir($file_path);

        $plugin_data = [];

        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                $handle = fopen($file_path . $file, 'r');
                $counter = 0;
                while ($counter < 20 || !feof($handle)) {
                    $line = fgets($handle);

                    if (preg_match('/Plugin Name: ?(.*)/', $line, $plugin_name) && isset($plugin_name[1]) ) {
                        $plugin_data['name'] = $plugin_name[1];
                    }

                    if (preg_match('/Version: ?(.*)/', $line, $plugin_ver) && isset($plugin_ver[1])) {
                        $plugin_data['version'] = $plugin_ver[1];
                    }

                    $counter++;
                }
                fclose($handle);
            }

            if (count($plugin_data) >= 2) break;
        }

        $plugin_data['slug'] = $this->makeSlug($plugin_data['name'], $plugin_data['version']);

        $path = $plugin->storeAs('public', $plugin_data['slug'] . '.zip');

        Plugin::create([
            'name' => $plugin_data['name'],
            'slug' => $plugin_data['slug'],
            'version' => $plugin_data['version'],
            'link' => asset("storage/${plugin_data['slug']}.zip"),
        ]);

        return $path;
    }

    protected function makeSlug($plugin_name, $plugin_version = '0.0.0') {
        $str = "$plugin_name $plugin_version";
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        return $str;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Plugin  $plugin
     * @return \Illuminate\Http\Response
     */
    public function show(Plugin $plugin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Plugin  $plugin
     * @return \Illuminate\Http\Response
     */
    public function edit(Plugin $plugin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Plugin  $plugin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plugin $plugin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Plugin  $plugin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plugin $plugin)
    {
        //
    }
}

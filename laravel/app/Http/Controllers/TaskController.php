<?php

namespace App\Http\Controllers;

use App\Folder;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Folder $folder)
    {
        // $folders = Folder::all();
        // $current_folder = Folder::find($folder);

        // if(is_null($current_folder)){
        //     abort(404);
        // }

        // $tasks = $current_folder->tasks()->get();
        
        // $request->user();

        if(Auth::getuser()->id !== $folder->user_id)
        {
            abort(403);
        }

        $folders = Auth::getuser()->folders()->get();

        $tasks = $folder->tasks()->get();

        return view('tasks/index',[
            'folders' => $folders,
            'current_folder_id' => $folder->id,
            'tasks' => $tasks,
        ]);
    }

    public function showCreateForm(Folder $folder)
    {
        return view('tasks/create', [
            'folder_id' => $folder->id,
        ]);
    }

    public function create(Folder $folder,CreateTask $request)
    {
        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;
    
        $folder->tasks()->save($task);
    
        return redirect()->route('tasks.index', [
            'folder' => $folder->id,
        ]);
    }

    public function showEditForm(Folder $folder, Task $task)
    {
        $this->checkRelation($folder,$task);

        return view('tasks/edit', [
            'task' => $task,
        ]);
    }
    

    public function edit(Folder $folder, Task $task, EditTask $request)
    {
        $this->checkRelation($folder,$task);

        $task->title = $request->title;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->save();
    
        return redirect()->route('tasks.index', [
            'folder' => $task->folder_id,
        ]);
    }

    private function checkRelation(Folder $folder, Task $task)
    {
        if($folder->id !== $task->folder_id)
        {
            abort(404);
        }
    }
}

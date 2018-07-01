<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();

        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;
        
        /* ログインユーザー */
        $user = \Auth::user();

        return view('tasks.create', [
            'task' => $task,
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        /*
        $task = new Task;
        $task->status = $request->status; 
        $task->content = $request->content;
        $task->save();
        */
        
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
        /* タスクのIDを取得 */
        $task = Task::find($id);
        
        /* ログインユーザーを取得 */
        $user = \Auth::user();
        
        /* ログインユーザーがそのタスクのIDを所有しているかのチェック */
        if (\Auth::user()->id === $task->user_id) {
        
        /* タスク詳細ページを表示 */
        return view('tasks.show', [
            'task' => $task,
            'user' => $user,
        ]);
        }
        /* TOPページにリダイレクト */
        else {
            return redirect('/');
        }
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::find($id);
        
        /* ログインユーザー */
        $user = \Auth::user();

        if (\Auth::user()->id === $task->user_id) {
        return view('tasks.edit', [
            'task' => $task,
            'user' => $user,
        ]);
        }
        else {
            return redirect('/');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        $task = Task::find($id);
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*
        $task = Task::find($id);
        $task->delete();
        return redirect('/');
        */
        
        $task = Task::find($id);

        if (\Auth::user()->id === $task->user_id) {
            $task->delete();
        }

        return redirect('/');
    }
}

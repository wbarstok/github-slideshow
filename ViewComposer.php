<?php

namespace App\Providers;

use Illuminate\Contracts\View\View;
use App\User;
use App\Admin;
use Auth;
use DB;

class ViewComposer {

    protected $customerId, $userDatas, $notifCount, $notificationDatas, $dashboardLink, $changePasswordLink, $profileLink, $formNotifEditAction, $formNotifUpdateAction, $adminCount,$userCount, $taskCount, $projectCount, $clientCount;

    /**
     * Create a new ViewComposer instance.
     */
    public function __construct()
    {
        //notification datas
        $notifStatus = 0;
        
        if(Auth::user() !== null){
            $userType = Auth::user()->user_type;
            $customerId = Auth::user()->id;
        }else{
            $userType = 'guest';
            $customerId = null;
        }

        $companyInfo = DB::table('company_info')->first();
        $navservices = DB::table('services')->get();

        $notificationCount = DB::table('notifications')
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            ->where('status','=',$notifStatus)
            ->count();

        $notificationDatas = DB::table('notifications')
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            ->where('status','=',$notifStatus)
            ->orderBy('date','DESC')
            ->get();

        $userDatas = DB::table('users')->get();
        $adminDatas = DB::table('admins')->get();

        if ($userType == 'admin') {
            $userDatas = $adminDatas;
            $profileLink = 'profil-admin.index';
            $changePasswordLink = 'admin.edit.password';
            $formNotifEditAction = 'notifikasi-admin.edit';
            $formNotifUpdateAction = 'notifikasi-admin.update';
            $dashboardLink  = 'admin.dashboard';
        }else{
            $userDatas = $userDatas;
            $profileLink = 'profil-user.index';
            $changePasswordLink = 'user.edit.password';
            $formNotifEditAction = 'notifikasi-user.edit';
            $formNotifUpdateAction = 'notifikasi-user.update';
            $dashboardLink  = 'user.index';
        }
        
        $this->companyInfo = $companyInfo;
        $this->customerId = $customerId;
        $this->userDatas = $userDatas;
        $this->notifCount = $notificationCount;
        $this->notifDatas = $notificationDatas;
        $this->changePasswordLink = $changePasswordLink;
        $this->profileLink = $profileLink;
        $this->formNotifEditAction = $formNotifEditAction;
        $this->formNotifUpdateAction = $formNotifUpdateAction;
        $this->dashboardLink = $dashboardLink;
        
        //custom data
        $adminCount = DB::table('admins')->where('deleted_at',null)->count();
        $userCount = DB::table('users')->where('deleted_at',null)->count();
        $taskCount = DB::table('tasks')->where('task_status','!=',3)->count();
        $projectCount = DB::table('project_category')->count();
        $clientCount = DB::table('clients')->count();
        //link
        $this->adminCount = $adminCount;
        $this->userCount = $userCount;
        $this->taskCount = $taskCount;
        $this->projectCount = $projectCount;
        $this->clientCount = $clientCount;
    }

    /**
     * Compose the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('companyInfo', $this->companyInfo);
        $view->with('customerId', $this->customerId);
        $view->with('userDatas', $this->userDatas);
        $view->with('notifCount', $this->notifCount);
        $view->with('notifDatas', $this->notifDatas);
        $view->with('profileLink', $this->profileLink);
        $view->with('changePasswordLink', $this->changePasswordLink);
        $view->with('formNotifEditAction', $this->formNotifEditAction);
        $view->with('formNotifUpdateAction', $this->formNotifUpdateAction);
        $view->with('dashboardLink', $this->dashboardLink);
        //custom data
        $view->with('adminCount', $this->adminCount);
        $view->with('userCount', $this->userCount);
        $view->with('taskCount', $this->taskCount);
        $view->with('projectCount', $this->projectCount);
        $view->with('clientCount', $this->clientCount);
    }

}

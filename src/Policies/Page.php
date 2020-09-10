<?php

namespace Armincms\Home\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Core\User\Models\User;
use Armincms\Home\Page as Model;

class Page
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any pages.
     *
     * @param  \Core\User\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the page.
     *
     * @param  \Core\User\Models\User  $user
     * @param  \Armincms\Home\Page  $page
     * @return mixed
     */
    public function view(User $user, Model $page)
    {
        return true;
    }

    /**
     * Determine whether the user can create pages.
     *
     * @param  \Core\User\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the page.
     *
     * @param  \Core\User\Models\User  $user
     * @param  \Armincms\Home\Page  $page
     * @return mixed
     */
    public function update(User $user, Model $page)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the page.
     *
     * @param  \Core\User\Models\User  $user
     * @param  \Armincms\Home\Page  $page
     * @return mixed
     */
    public function delete(User $user, Model $page)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the page.
     *
     * @param  \Core\User\Models\User  $user
     * @param  \Armincms\Home\Page  $page
     * @return mixed
     */
    public function restore(User $user, Model $page)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the page.
     *
     * @param  \Core\User\Models\User  $user
     * @param  \Armincms\Home\Page  $page
     * @return mixed
     */
    public function forceDelete(User $user, Model $page)
    {
        return true;
    }

    /**
     * Determine whether the user can publish the page.
     *
     * @param  \Core\User\Models\User  $user 
     * @param  \Armincms\Home\Page  $page
     * @return mixed
     */
    public function publish(User $user, Model $page)
    {
        return true;
    }
}

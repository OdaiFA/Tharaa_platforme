<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Article $article): bool
    {
        return $user->isAdmin() || $article->is_published;
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}

<?php

namespace App\Http\Controllers\Traits;

use App\Models\Post;
use Illuminate\Http\Request;

trait ValidatesPostAttributes
{
    public function getPostBodyAttribute(Request $request): string
    {
        return $this->validatePostBody($request, 'body'.$this->getPostCommentIdAttribute($request));
    }

    public function getPostCommentIdAttribute(Request $request): string|null
    {
        return ! $request->input('comment_on_post') ? null : $this->validatePostCommentId($request);
    }

    protected function validatePostBody(Request $request, string $name): string
    {
        return $request->validate([
            $name => [
                'required',
                'string',
                'max:2000',
            ]
        ])[$name];
    }

    protected function validatePostCommentId(Request $request): string
    {
        return $request->validate([
            'comment_on_post' => [
                'nullable', 
                'string', 
                'exists:posts,id', 
                function ($attribute, $value, $fail) {
                    if (Post::query()->where('id', $value)->first()->comment_on_post !== null) {
                        $fail('Commenting on a comment is not allowed. Invalid '.$attribute.' value.');
                    }
                }
            ],
        ])['comment_on_post'];
    }
}
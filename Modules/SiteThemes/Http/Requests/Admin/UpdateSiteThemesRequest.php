<?php
namespace Modules\SiteThemes\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteThemesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            
            'title' => 'required|unique:site_themes,title,'.$this->route('site_theme'),
            'theme_title_key' => 'required|unique:site_themes,theme_title_key,'.$this->route('site_theme'),
        ];
    }
}

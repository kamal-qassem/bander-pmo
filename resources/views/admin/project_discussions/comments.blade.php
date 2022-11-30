<?php
$query = \App\ProjectDiscussionComment::where('project_discussion_comments.project_id', '=', $project->id );
$query->with("project");
$query->with("created_name");
$query->with("contact");
$query->with("parent_comment");            

$query->select([
    'project_discussion_comments.id',
    'project_discussion_comments.description',
    'project_discussion_comments.created_by_id',
    'project_discussion_comments.project_id',
    'project_discussion_comments.discussion_id',
    'project_discussion_comments.parent_id',
    'project_discussion_comments.attachment',
    'project_discussion_comments.created_at',
]);
if ( ! empty( $topic->id ) ) {
    $query->where('project_discussion_comments.discussion_id', '=', $topic->id );
}
$query->leftJoin('project_discussion_comments as child', 'child.parent_id', '=', 'project_discussion_comments.id');

$query->orderBy('project_discussion_comments.id');

$comments = $query->orderBy('child.parent_id')->paginate(10);
?>
<div class="container">
  <div class="row">
    <div class="col-md-10">
      <h2 class="page-header">Comments</h2>
        <section class="comment-list">

          <!-- First Comment -->
          @forelse ( $comments as $commentrow )         
          <article class="row">
            <?php
            $name = $commentrow->created_name->name;
            $img = asset('images/avatar-32x32.jpg');
            $details = $commentrow->created_name;
            if ( $details && $details->contact_reference_id ) {
                $contact = \App\Contact::find( $details->contact_reference_id );
                if ( $contact ) {
                    $img = asset(env('UPLOAD_PATH').'/thumb/'.$contact->thumbnail);
                }
            } 
            $class_thumb = 'col-sm-2 hidden-xs';
            $class_comment = 'col-md-8 col-sm-8';
            if ( ! empty( $commentrow->parent_id ) ) {
              $class_thumb = 'col-sm-2 col-md-offset-2 col-sm-offset-0 hidden-xs';
              $class_comment = 'col-md-9 col-sm-9';
            }
            ?>
            <a href="{{route('admin.users.show', $commentrow->created_by_id)}}">
            <div class="{{$class_thumb}}">
              <figure class="thumbnail">
                <img class="img-responsive" src="{{$img}}" />
                <figcaption class="text-center">{{$name}}</figcaption>
              </figure>
            </div>
            </a>
            <div class="{{$class_comment}}">
              <div class="panel panel-default arrow left">
                <div class="panel-body">
                  <header class="text-left">
                    
                    <time class="comment-date" datetime="16-12-2014 01:05"><i class="fa fa-clock-o"></i> {{digiDate( $commentrow->created_at, true )}}</time>
                  </header>
                  <div class="comment-post">
                    <p>
                      <?php
                      $description = $commentrow->description;
                      if ( ! empty( $commentrow->attachment ) ) {

                          $attachment = '<p><a href="'. route('admin.home.media-file-download', [ 'model' => 'ProjectDiscussionComment', 'field' => 'attachment', 'record_id' => $commentrow->id ]) .'" >'.trans('global.project-discussions.attachment').'</a></p>';
                          $description .= $attachment;
                      }
                      ?>
                      {!!$description!!}</p>
                  </div>
                    <p style="float:right;">
                    <?php
                    if ( Auth::id() == $commentrow->created_by_id ) {
                        $route_edit = route( 'admin.project_discussions.comments', ['project_id' => $commentrow->project_id, 'discussion_id' => $commentrow->discussion_id, 'operation' => 'edit', 'id' => $commentrow->id ]);
                        echo '<a href="'.$route_edit.'" class="btn btn-info btn-sm"><i class="fa fa-edit"></i>'.trans('global.app_edit').'</a><span>&#124;</span>';
                    }

                    $route_edit = route( 'admin.project_discussions.comments', ['project_id' => $commentrow->project_id, 'discussion_id' => $commentrow->discussion_id, 'operation' => 'answer', 'id' => $commentrow->id ]);
                    echo '<a href="'.$route_edit.'" class="btn btn-success btn-sm"><i class="fa fa-reply"></i> '.trans('global.project-discussions.answer').'</a>';
                    ?>
                    
                    
                    
                    <form method="POST" action="{{route( 'admin.project_comment.destroy', ['project_id' => $commentrow->project_id, 'id' => $commentrow->id ])}}" accept-charset="UTF-8" style="display: inline-block; float:right;" onsubmit="return confirm(window.are_you_sure );">
                    <input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{csrf_token()}}"><button class="btn btn-xs btn-danger"  type="submit" value="{{trans('global.app_delete')}}"><i class="fa fa-trash"></i> {{trans('global.app_delete')}}</button><span>&#124;</span></form>                    
                    </p>
                </div>
              </div>
            </div>
          </article>
          @empty
            <p>No Records</p>
          @endforelse
        </section>
        {{ $comments->links() }}
    </div>
  </div>
</div>


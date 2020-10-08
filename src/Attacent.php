<?php

namespace Cc\Labems;

use Cc\Labems\Facades\Auth;
use Cc\Labems\Models\Attachment;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Attacent
{
    private $uid = 0;
    private $pageSize = 20;
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk(LABEMS_ENTRY);
        $this->uid = Auth::id();
    }

    public function url($path)
    {
        return $this->disk->url($path);
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function upload(UploadedFile $file)
    {
        $allowedExt = config('labems.' . LABEMS_ENTRY . '.attachment.allowed_ext', []);
        $type = strstr($file->getMimeType(), '/', true);
        if ($type && array_key_exists($type, $allowedExt) && (1 === preg_match('/^(' . $allowedExt[$type] . ')$/i', $file->extension()))) {
            $path = $this->disk->putFile($type . '/' . date('Y/m/d'), $file);
            if (empty($path)) {
                throw new Exception('put file failed');
            }
            $attachment = new Attachment();
            $attachment->type = $type;
            $attachment->path = $path;
            $attachment->filename = $file->getClientOriginalName();
            $attachment->uid = $this->uid;
            $attachment->save();
            return [
                'url' => $attachment->url,
                'filename' => $attachment->filename,
                'path' => $attachment->path,
                'id' => $attachment->id,
                'type' => $attachment->type,
            ];
        }
        throw new Exception('invalid file');
    }

    public function getList($page = 1, $type = 'image', $filter = ['year' => null, 'month' => null])
    {
        $page = max(intval($page), 1);
        $attachment = Attachment::where('uid', $this->uid)
            ->where('type', $type)
            ->where(function ($query) use ($filter) {
                if (!empty($filter['year'])) {
                    $query->whereYear('created_at', $filter['year']);
                    if (!empty($filter['month'])) {
                        $query->whereMonth('created_at', $filter['month']);
                    }
                }
            });
        return [
            'total' => $attachment->count(),
            'pageSize' => $this->pageSize,
            'page' => $page,
            'data' => $attachment
                ->orderBy('id', 'desc')
                ->offset(($page - 1) * $this->pageSize)
                ->limit($this->pageSize)
                ->get(),
        ];
    }

    public function delete($id)
    {
        $attachment = Attachment::find($id);
        if ($attachment) {
            $attachment->delete();
            return  $this->disk->delete($attachment->path);
        }
        throw new Exception('attachment don\'t exist');
    }
}

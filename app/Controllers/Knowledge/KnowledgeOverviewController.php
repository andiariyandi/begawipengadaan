<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\ArticleModel;
use App\Models\Knowledge\KnowledgeCategoryModel;
use App\Models\Knowledge\KnowledgeMediaItemModel;
use App\Models\Knowledge\TagModel;

class KnowledgeOverviewController extends BaseKnowledgeController
{
    public function index(): string
    {
        $db = $this->db;

        // 1. Core KPIs
        $articleStats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'article')->where('deleted_at IS NULL')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'article')->where('workflow_status', 'published')->where('deleted_at IS NULL')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_articles')->where('type', 'article')->where('workflow_status', 'draft')->where('deleted_at IS NULL')->countAllResults(),
            'in_review' => (int) $db->table('knowledge_articles')->where('type', 'article')->whereIn('workflow_status', ['in_review', 'submitted'])->where('deleted_at IS NULL')->countAllResults(),
        ];

        $faqStats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'faq')->where('deleted_at IS NULL')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'faq')->where('workflow_status', 'published')->where('deleted_at IS NULL')->countAllResults(),
        ];

        $caseStats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'case_study')->where('deleted_at IS NULL')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'case_study')->where('workflow_status', 'published')->where('deleted_at IS NULL')->countAllResults(),
        ];

        $templateStats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'template')->where('deleted_at IS NULL')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'template')->where('workflow_status', 'published')->where('deleted_at IS NULL')->countAllResults(),
        ];

        // Media breakdown
        $infographicsCount = (int) $db->table('knowledge_media')->whereIn('type', ['infographic', 'image', 'poster', 'flyer'])->countAllResults();
        $videosCount       = (int) $db->table('knowledge_media')->whereIn('type', ['youtube', 'video_file', 'video'])->countAllResults();
        $galleryCount      = (int) $db->table('knowledge_media')->where('type', 'gallery')->countAllResults();
        $totalMediaAll     = (int) $db->table('knowledge_media')->countAllResults();

        $categoriesCount   = (int) $db->table('knowledge_categories')->where('is_active', 1)->countAllResults();
        $tagsCount         = (int) $db->table('knowledge_tags')->countAllResults();
        $featuredCount     = (int) $db->table('knowledge_articles')->where('featured', 1)->where('deleted_at IS NULL')->countAllResults()
                           + (int) $db->table('knowledge_media')->where('featured', 1)->countAllResults();

        // 2. Recent Articles & Case Studies & Templates
        $recentArticles = $db->table('knowledge_articles ka')
            ->select('ka.*, u.name AS author_name, fa.path AS cover_path')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
            ->where('ka.deleted_at IS NULL')
            ->orderBy('ka.updated_at', 'DESC')
            ->limit(8)
            ->get()->getResultArray();

        // 3. Recent Media Items
        $recentMedia = $db->table('knowledge_media km')
            ->select('km.*, fa.path AS file_path, ft.path AS thumb_path')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->orderBy('km.updated_at', 'DESC')
            ->limit(6)
            ->get()->getResultArray();

        // 4. Categories list with count
        $categories = (new KnowledgeCategoryModel())->activeFlat();

        return view('dashboard/knowledge/overview/index', [
            'title'             => 'Ringkasan Bunga Rampai | Dashboard BegawiPBJ',
            'pageTitle'         => 'Dashboard Bunga Rampai (Knowledge Management)',
            'activeMenu'        => 'knowledge',
            'activeSubmenu'     => 'overview',
            'articleStats'      => $articleStats,
            'faqStats'          => $faqStats,
            'caseStats'         => $caseStats,
            'templateStats'     => $templateStats,
            'infographicsCount' => $infographicsCount,
            'videosCount'       => $videosCount,
            'galleryCount'      => $galleryCount,
            'totalMediaAll'     => $totalMediaAll,
            'categoriesCount'   => $categoriesCount,
            'tagsCount'         => $tagsCount,
            'featuredCount'     => $featuredCount,
            'recentArticles'    => $recentArticles,
            'recentMedia'       => $recentMedia,
            'categories'        => $categories,
        ]);
    }
}

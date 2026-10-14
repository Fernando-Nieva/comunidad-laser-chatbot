<?php

return [
    'top_k' => (int) env('RAG_TOP_K', 5),
    'relevance_threshold' => (float) env('RAG_RELEVANCE_THRESHOLD', 0.75),
    'min_results' => (int) env('RAG_MIN_RESULTS', 2),

    // Missing info policy
    'required_fields' => ['material', 'job_type'],
    'recommended_fields' => ['thickness_mm', 'machine_power_w'],
    'max_questions_in_row' => (int) env('RAG_MAX_QUESTIONS_IN_ROW', 3),

    // Retriever filters
    'filter_by_material' => env('RAG_FILTER_BY_MATERIAL', true),
    'filter_by_job_type' => env('RAG_FILTER_BY_JOB_TYPE', true),

    // Sufficiency evaluator
    'require_material_match' => env('RAG_REQUIRE_MATERIAL_MATCH', true),
];
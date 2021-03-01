<?php

namespace App;

use http\Exception\InvalidArgumentException;

class Analyzer
{
    protected $chapter_split_sec;
    protected $subchapter_split_sec;
    protected $max_segment_duration;

    protected $chapters = [];

    /**
     * Analyzer constructor.
     * @param int $chapter_split_sec
     * @param int $subchapter_split_sec
     * @param int $max_segment_duration
     */
    public function __construct(
        int $chapter_split_sec, int $subchapter_split_sec, int $max_segment_duration
    )
    {
        if ($subchapter_split_sec >= $chapter_split_sec) {
            throw new InvalidArgumentException(
                'Chapter split sec must be greater that subchapter split sec'
            );
        }

        $this->chapter_split_sec = $chapter_split_sec;
        $this->subchapter_split_sec = $subchapter_split_sec;
        $this->max_segment_duration = $max_segment_duration;
    }

    /**
     * Split metadata to chapter/subchapter array.
     *
     * @param array $metadata
     * @throws \Exception
     */
    public function splitMetadata(array $metadata)
    {
        $cur_chapter = 1;
        $cur_subchapter = 0;
        $segment_duration = 0;
        $is_split = false;

        // add first chapter row (from zero to first from occurrence)
        $this->addChapterRow($cur_chapter, $cur_subchapter, $metadata[0]['from']);

        foreach ($metadata as $duration) {

            $from = $this->makeTimeFromString($duration['from']);
            $from_duration = $this->calculateIntervalDuration($from);

            $until = $this->makeTimeFromString($duration['until']);
            $until_duration = $this->calculateIntervalDuration($until);

            $silence = $until_duration - $from_duration;
            $segment_duration += $from_duration;

            $is_split = false;

            // case when silence duration splits chapters
            if ($silence >= $this->chapter_split_sec) {
                $cur_chapter++;
                $cur_subchapter = 0;
                $segment_duration = 0;
                $this->addChapterRow($cur_chapter, $cur_subchapter, $duration['until']);

                continue;
            }

            // case when current segment longer than our segment split time
            // and silence enough long for splitting chapter to segments
            if (
                $segment_duration >= $this->max_segment_duration &&
                $silence >= $this->subchapter_split_sec
            ) {
                $cur_subchapter++;
                $segment_duration = 0;
                $is_split = true;
            }

            if ($is_split) {
                $this->addChapterRow($cur_chapter, $cur_subchapter, $duration['until']);
            }
        }

        return $this->chapters;
    }

    /**
     * Make DateInterval object from the source date string
     *
     * @param string $date
     * @return \DateInterval
     * @throws \Exception
     */
    protected function makeTimeFromString(string $date)
    {
        // remove milliseconds to convert string to valid DateInterval date
        $date = explode('.', $date);
        $date = isset($date[1]) ? $date[0].'S' : $date[0];

        // TODO: throw exception
        return new \DateInterval($date);
    }

    /**
     * Get silence duration between 2 intervals.
     *
     * @param \DateInterval $from
     * @param \DateInterval $until
     * @return float|int
     */
    protected function calculateIntervalDuration(\DateInterval $timing)
    {
        $s = $timing->s;
        $m = $timing->m * 60;
        $h = $timing->h * 60 * 60;

        return $h + $m + $s;
    }

    /**
     * Add new chapter row.
     *
     * @param int $cur_chapter
     * @param int $cur_subchapter
     * @param string $start_at
     */
    protected function addChapterRow(int $cur_chapter, int $cur_subchapter, string $start_at)
    {
        $title = "Chapter {$cur_chapter}";
        if ($cur_subchapter > 0) {
            $title .= ", part {$cur_subchapter}";
        }

        $this->chapters[] = [
            'title' => $title,
            'offset' => $start_at,
        ];
    }

}
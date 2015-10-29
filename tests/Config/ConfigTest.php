<?php
namespace Neat\Test\Config;

use Mockery;
use Mockery\Mock;
use Neat\Config\Config;
use Neat\Loader\FileLoader;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    private $masterSettings = [
        'offset1' => 'offset1',
        'offset2' => null,
        'path1' => ['path1_segment' => 'path1_segment_value'],
        'path2' => ['path2_segment' => null],
    ];

    /** @var array */
    private $branchSettings1 = [
        'branch1_offset' => 'branch1_offset_value',
        'branch1_path' => ['branch1_path_segment' => 'branch1_path_value'],
    ];

    /** @var array */
    private $branchSettings2 = [
        'branch2_offset' => 'branch2_offset_value',
        'branch2_path' => ['branch2_path_segment' => 'branch2_path_value'],
    ];

    /**
     * @test
     * @return Config
     */
    public function loadMasterConfig()
    {
        /** @var Mock|FileLoader $fileLoader */
        $fileLoader = Mockery::mock('Neat\Loader\FileLoader');
        $fileLoader->shouldReceive('load')->with('config.master', 'config')->once()->andReturn($this->masterSettings);

        $config = new Config($fileLoader);
        $config
            ->loadFile('config.master')
            ->setBranch('offset2', 'config.branch1')
            ->setBranch('path2.path2_segment', 'config.branch2');

        $this->assertSame('offset1', $config->get('offset1'));
        $this->assertSame('path1_segment_value', $config->get('path1.path1_segment'));

        return $config;
    }

    /**
     * @test
     * @depends loadMasterConfig
     * @param Config $config
     */
    public function loadBranchConfig(Config $config)
    {
        /** @var Mock|FileLoader $fileLoader */
        $fileLoader = $config->getFileLoader();
        $fileLoader->shouldReceive('load')->with('config.branch1', 'config')->once()->andReturn($this->branchSettings1);
        $fileLoader->shouldReceive('load')->with('config.branch2', 'config')->once()->andReturn($this->branchSettings2);

        $this->assertInstanceOf('Neat\Config\Config', $config->get('offset2'));
        $this->assertSame('branch1_offset_value', $config->get('offset2.branch1_offset'));
        $this->assertSame('branch1_path_value', $config->get('offset2.branch1_path.branch1_path_segment'));

        $this->assertInstanceOf('Neat\Config\Config', $config->get('path2.path2_segment'));
        $this->assertSame('branch2_offset_value', $config->get('path2.path2_segment.branch2_offset'));
        $this->assertSame('branch2_path_value', $config->get('path2.path2_segment.branch2_path.branch2_path_segment'));
    }
}

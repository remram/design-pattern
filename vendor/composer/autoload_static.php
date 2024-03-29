<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitde5d04a148349d2ae59223b494db5dbb
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'StrategyPattern\\' => 16,
            'SPLObserverPattern\\' => 19,
        ),
        'O' => 
        array (
            'ObserverPattern\\' => 16,
        ),
        'D' => 
        array (
            'DecoratorPattern\\menu\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'StrategyPattern\\' => 
        array (
            0 => __DIR__ . '/../..' . '/strategy/walk/src',
        ),
        'SPLObserverPattern\\' => 
        array (
            0 => __DIR__ . '/../..' . '/observer/news_paper_spl/src',
        ),
        'ObserverPattern\\' => 
        array (
            0 => __DIR__ . '/../..' . '/observer/news_paper/src',
        ),
        'DecoratorPattern\\menu\\' => 
        array (
            0 => __DIR__ . '/../..' . '/decorator/menu/src',
        ),
    );

    public static $classMap = array (
        'DecoratorPattern\\menu\\Dish' => __DIR__ . '/../..' . '/decorator/menu/src/Dish.php',
        'DecoratorPattern\\menu\\Fries' => __DIR__ . '/../..' . '/decorator/menu/src/Fries.php',
        'DecoratorPattern\\menu\\Salad' => __DIR__ . '/../..' . '/decorator/menu/src/Salad.php',
        'DecoratorPattern\\menu\\Side' => __DIR__ . '/../..' . '/decorator/menu/src/Side.php',
        'DecoratorPattern\\menu\\Steak' => __DIR__ . '/../..' . '/decorator/menu/src/Steak.php',
        'DecoratorPattern\\menu\\Tofu' => __DIR__ . '/../..' . '/decorator/menu/src/Tofu.php',
        'ObserverPattern\\NewsPaper' => __DIR__ . '/../..' . '/observer/news_paper/src/NewsPaper.php',
        'ObserverPattern\\NzzPublisher' => __DIR__ . '/../..' . '/observer/news_paper/src/NzzPublisher.php',
        'ObserverPattern\\Publisher' => __DIR__ . '/../..' . '/observer/news_paper/src/Publisher.php',
        'ObserverPattern\\Subscriber' => __DIR__ . '/../..' . '/observer/news_paper/src/Subscriber.php',
        'ObserverPattern\\SubscriberA' => __DIR__ . '/../..' . '/observer/news_paper/src/SubscriberA.php',
        'ObserverPattern\\SubscriberB' => __DIR__ . '/../..' . '/observer/news_paper/src/SubscriberB.php',
        'ObserverPattern\\SubscriberC' => __DIR__ . '/../..' . '/observer/news_paper/src/SubscriberC.php',
        'SPLObserverPattern\\NewsPaper' => __DIR__ . '/../..' . '/observer/news_paper_spl/src/NewsPaper.php',
        'SPLObserverPattern\\NzzPublisher' => __DIR__ . '/../..' . '/observer/news_paper_spl/src/NzzPublisher.php',
        'SPLObserverPattern\\Publisher' => __DIR__ . '/../..' . '/observer/news_paper_spl/src/Publisher.php',
        'SPLObserverPattern\\SubscriberA' => __DIR__ . '/../..' . '/observer/news_paper_spl/src/SubscriberA.php',
        'SPLObserverPattern\\SubscriberB' => __DIR__ . '/../..' . '/observer/news_paper_spl/src/SubscriberB.php',
        'SPLObserverPattern\\SubscriberC' => __DIR__ . '/../..' . '/observer/news_paper_spl/src/SubscriberC.php',
        'StrategyPattern\\Dog' => __DIR__ . '/../..' . '/strategy/walk/src/Dog.php',
        'StrategyPattern\\FastWalk' => __DIR__ . '/../..' . '/strategy/walk/src/FastWalk.php',
        'StrategyPattern\\Husky' => __DIR__ . '/../..' . '/strategy/walk/src/Husky.php',
        'StrategyPattern\\NormalWalk' => __DIR__ . '/../..' . '/strategy/walk/src/NormalWalk.php',
        'StrategyPattern\\Walk' => __DIR__ . '/../..' . '/strategy/walk/src/Walk.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitde5d04a148349d2ae59223b494db5dbb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitde5d04a148349d2ae59223b494db5dbb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitde5d04a148349d2ae59223b494db5dbb::$classMap;

        }, null, ClassLoader::class);
    }
}

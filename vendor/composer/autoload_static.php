<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd0d9ec57d4a198e8c0a84af8a5284dcb
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WpRediSearch\\' => 13,
        ),
        'V' => 
        array (
            'Vaites\\ApacheTika\\' => 18,
        ),
        'S' => 
        array (
            'SevenFields\\' => 12,
        ),
        'P' => 
        array (
            'Predis\\' => 7,
        ),
        'F' => 
        array (
            'FKRediSearch\\' => 13,
        ),
        'A' => 
        array (
            'Asika\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WpRediSearch\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Vaites\\ApacheTika\\' => 
        array (
            0 => __DIR__ . '/..' . '/vaites/php-apache-tika/src',
        ),
        'SevenFields\\' => 
        array (
            0 => __DIR__ . '/..' . '/foadyousefi/seven-fields/src',
        ),
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
        'FKRediSearch\\' => 
        array (
            0 => __DIR__ . '/..' . '/front/redisearch/src',
        ),
        'Asika\\' => 
        array (
            0 => __DIR__ . '/..' . '/asika/pdf2text/src',
        ),
    );

    public static $classMap = array (
        'Asika\\Pdf2text' => __DIR__ . '/..' . '/asika/pdf2text/src/Pdf2text.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'FKRediSearch\\Document' => __DIR__ . '/..' . '/front/redisearch/src/Document.php',
        'FKRediSearch\\Fields\\AbstractField' => __DIR__ . '/..' . '/front/redisearch/src/Fields/AbstractField.php',
        'FKRediSearch\\Fields\\FieldInterface' => __DIR__ . '/..' . '/front/redisearch/src/Fields/FieldInterface.php',
        'FKRediSearch\\Fields\\GeoField' => __DIR__ . '/..' . '/front/redisearch/src/Fields/GeoField.php',
        'FKRediSearch\\Fields\\GeoLocation' => __DIR__ . '/..' . '/front/redisearch/src/Fields/GeoLocation.php',
        'FKRediSearch\\Fields\\Noindex' => __DIR__ . '/..' . '/front/redisearch/src/Fields/Noindex.php',
        'FKRediSearch\\Fields\\NumericField' => __DIR__ . '/..' . '/front/redisearch/src/Fields/NumericField.php',
        'FKRediSearch\\Fields\\Sortable' => __DIR__ . '/..' . '/front/redisearch/src/Fields/Sortable.php',
        'FKRediSearch\\Fields\\TagField' => __DIR__ . '/..' . '/front/redisearch/src/Fields/TagField.php',
        'FKRediSearch\\Fields\\TextField' => __DIR__ . '/..' . '/front/redisearch/src/Fields/TextField.php',
        'FKRediSearch\\Index' => __DIR__ . '/..' . '/front/redisearch/src/Index.php',
        'FKRediSearch\\Query\\Query' => __DIR__ . '/..' . '/front/redisearch/src/Query/Query.php',
        'FKRediSearch\\Query\\QueryBuilder' => __DIR__ . '/..' . '/front/redisearch/src/Query/QueryBuilder.php',
        'FKRediSearch\\Query\\SearchResult' => __DIR__ . '/..' . '/front/redisearch/src/Query/SearchResult.php',
        'FKRediSearch\\RedisRaw\\AbstractRedisRawClient' => __DIR__ . '/..' . '/front/redisearch/src/RedisRaw/AbstractRedisRawClient.php',
        'FKRediSearch\\RedisRaw\\PredisAdapter' => __DIR__ . '/..' . '/front/redisearch/src/RedisRaw/PredisAdapter.php',
        'FKRediSearch\\RedisRaw\\RedisRawClientInterface' => __DIR__ . '/..' . '/front/redisearch/src/RedisRaw/RedisRawClientInterface.php',
        'FKRediSearch\\Setup' => __DIR__ . '/..' . '/front/redisearch/src/Setup.php',
        'Predis\\Autoloader' => __DIR__ . '/..' . '/predis/predis/src/Autoloader.php',
        'Predis\\Client' => __DIR__ . '/..' . '/predis/predis/src/Client.php',
        'Predis\\ClientContextInterface' => __DIR__ . '/..' . '/predis/predis/src/ClientContextInterface.php',
        'Predis\\ClientException' => __DIR__ . '/..' . '/predis/predis/src/ClientException.php',
        'Predis\\ClientInterface' => __DIR__ . '/..' . '/predis/predis/src/ClientInterface.php',
        'Predis\\Cluster\\ClusterStrategy' => __DIR__ . '/..' . '/predis/predis/src/Cluster/ClusterStrategy.php',
        'Predis\\Cluster\\Distributor\\DistributorInterface' => __DIR__ . '/..' . '/predis/predis/src/Cluster/Distributor/DistributorInterface.php',
        'Predis\\Cluster\\Distributor\\EmptyRingException' => __DIR__ . '/..' . '/predis/predis/src/Cluster/Distributor/EmptyRingException.php',
        'Predis\\Cluster\\Distributor\\HashRing' => __DIR__ . '/..' . '/predis/predis/src/Cluster/Distributor/HashRing.php',
        'Predis\\Cluster\\Distributor\\KetamaRing' => __DIR__ . '/..' . '/predis/predis/src/Cluster/Distributor/KetamaRing.php',
        'Predis\\Cluster\\Hash\\CRC16' => __DIR__ . '/..' . '/predis/predis/src/Cluster/Hash/CRC16.php',
        'Predis\\Cluster\\Hash\\HashGeneratorInterface' => __DIR__ . '/..' . '/predis/predis/src/Cluster/Hash/HashGeneratorInterface.php',
        'Predis\\Cluster\\PredisStrategy' => __DIR__ . '/..' . '/predis/predis/src/Cluster/PredisStrategy.php',
        'Predis\\Cluster\\RedisStrategy' => __DIR__ . '/..' . '/predis/predis/src/Cluster/RedisStrategy.php',
        'Predis\\Cluster\\StrategyInterface' => __DIR__ . '/..' . '/predis/predis/src/Cluster/StrategyInterface.php',
        'Predis\\Collection\\Iterator\\CursorBasedIterator' => __DIR__ . '/..' . '/predis/predis/src/Collection/Iterator/CursorBasedIterator.php',
        'Predis\\Collection\\Iterator\\HashKey' => __DIR__ . '/..' . '/predis/predis/src/Collection/Iterator/HashKey.php',
        'Predis\\Collection\\Iterator\\Keyspace' => __DIR__ . '/..' . '/predis/predis/src/Collection/Iterator/Keyspace.php',
        'Predis\\Collection\\Iterator\\ListKey' => __DIR__ . '/..' . '/predis/predis/src/Collection/Iterator/ListKey.php',
        'Predis\\Collection\\Iterator\\SetKey' => __DIR__ . '/..' . '/predis/predis/src/Collection/Iterator/SetKey.php',
        'Predis\\Collection\\Iterator\\SortedSetKey' => __DIR__ . '/..' . '/predis/predis/src/Collection/Iterator/SortedSetKey.php',
        'Predis\\Command\\Command' => __DIR__ . '/..' . '/predis/predis/src/Command/Command.php',
        'Predis\\Command\\CommandInterface' => __DIR__ . '/..' . '/predis/predis/src/Command/CommandInterface.php',
        'Predis\\Command\\ConnectionAuth' => __DIR__ . '/..' . '/predis/predis/src/Command/ConnectionAuth.php',
        'Predis\\Command\\ConnectionEcho' => __DIR__ . '/..' . '/predis/predis/src/Command/ConnectionEcho.php',
        'Predis\\Command\\ConnectionPing' => __DIR__ . '/..' . '/predis/predis/src/Command/ConnectionPing.php',
        'Predis\\Command\\ConnectionQuit' => __DIR__ . '/..' . '/predis/predis/src/Command/ConnectionQuit.php',
        'Predis\\Command\\ConnectionSelect' => __DIR__ . '/..' . '/predis/predis/src/Command/ConnectionSelect.php',
        'Predis\\Command\\GeospatialGeoAdd' => __DIR__ . '/..' . '/predis/predis/src/Command/GeospatialGeoAdd.php',
        'Predis\\Command\\GeospatialGeoDist' => __DIR__ . '/..' . '/predis/predis/src/Command/GeospatialGeoDist.php',
        'Predis\\Command\\GeospatialGeoHash' => __DIR__ . '/..' . '/predis/predis/src/Command/GeospatialGeoHash.php',
        'Predis\\Command\\GeospatialGeoPos' => __DIR__ . '/..' . '/predis/predis/src/Command/GeospatialGeoPos.php',
        'Predis\\Command\\GeospatialGeoRadius' => __DIR__ . '/..' . '/predis/predis/src/Command/GeospatialGeoRadius.php',
        'Predis\\Command\\GeospatialGeoRadiusByMember' => __DIR__ . '/..' . '/predis/predis/src/Command/GeospatialGeoRadiusByMember.php',
        'Predis\\Command\\HashDelete' => __DIR__ . '/..' . '/predis/predis/src/Command/HashDelete.php',
        'Predis\\Command\\HashExists' => __DIR__ . '/..' . '/predis/predis/src/Command/HashExists.php',
        'Predis\\Command\\HashGet' => __DIR__ . '/..' . '/predis/predis/src/Command/HashGet.php',
        'Predis\\Command\\HashGetAll' => __DIR__ . '/..' . '/predis/predis/src/Command/HashGetAll.php',
        'Predis\\Command\\HashGetMultiple' => __DIR__ . '/..' . '/predis/predis/src/Command/HashGetMultiple.php',
        'Predis\\Command\\HashIncrementBy' => __DIR__ . '/..' . '/predis/predis/src/Command/HashIncrementBy.php',
        'Predis\\Command\\HashIncrementByFloat' => __DIR__ . '/..' . '/predis/predis/src/Command/HashIncrementByFloat.php',
        'Predis\\Command\\HashKeys' => __DIR__ . '/..' . '/predis/predis/src/Command/HashKeys.php',
        'Predis\\Command\\HashLength' => __DIR__ . '/..' . '/predis/predis/src/Command/HashLength.php',
        'Predis\\Command\\HashScan' => __DIR__ . '/..' . '/predis/predis/src/Command/HashScan.php',
        'Predis\\Command\\HashSet' => __DIR__ . '/..' . '/predis/predis/src/Command/HashSet.php',
        'Predis\\Command\\HashSetMultiple' => __DIR__ . '/..' . '/predis/predis/src/Command/HashSetMultiple.php',
        'Predis\\Command\\HashSetPreserve' => __DIR__ . '/..' . '/predis/predis/src/Command/HashSetPreserve.php',
        'Predis\\Command\\HashStringLength' => __DIR__ . '/..' . '/predis/predis/src/Command/HashStringLength.php',
        'Predis\\Command\\HashValues' => __DIR__ . '/..' . '/predis/predis/src/Command/HashValues.php',
        'Predis\\Command\\HyperLogLogAdd' => __DIR__ . '/..' . '/predis/predis/src/Command/HyperLogLogAdd.php',
        'Predis\\Command\\HyperLogLogCount' => __DIR__ . '/..' . '/predis/predis/src/Command/HyperLogLogCount.php',
        'Predis\\Command\\HyperLogLogMerge' => __DIR__ . '/..' . '/predis/predis/src/Command/HyperLogLogMerge.php',
        'Predis\\Command\\KeyDelete' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyDelete.php',
        'Predis\\Command\\KeyDump' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyDump.php',
        'Predis\\Command\\KeyExists' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyExists.php',
        'Predis\\Command\\KeyExpire' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyExpire.php',
        'Predis\\Command\\KeyExpireAt' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyExpireAt.php',
        'Predis\\Command\\KeyKeys' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyKeys.php',
        'Predis\\Command\\KeyMigrate' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyMigrate.php',
        'Predis\\Command\\KeyMove' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyMove.php',
        'Predis\\Command\\KeyPersist' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyPersist.php',
        'Predis\\Command\\KeyPreciseExpire' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyPreciseExpire.php',
        'Predis\\Command\\KeyPreciseExpireAt' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyPreciseExpireAt.php',
        'Predis\\Command\\KeyPreciseTimeToLive' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyPreciseTimeToLive.php',
        'Predis\\Command\\KeyRandom' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyRandom.php',
        'Predis\\Command\\KeyRename' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyRename.php',
        'Predis\\Command\\KeyRenamePreserve' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyRenamePreserve.php',
        'Predis\\Command\\KeyRestore' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyRestore.php',
        'Predis\\Command\\KeyScan' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyScan.php',
        'Predis\\Command\\KeySort' => __DIR__ . '/..' . '/predis/predis/src/Command/KeySort.php',
        'Predis\\Command\\KeyTimeToLive' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyTimeToLive.php',
        'Predis\\Command\\KeyType' => __DIR__ . '/..' . '/predis/predis/src/Command/KeyType.php',
        'Predis\\Command\\ListIndex' => __DIR__ . '/..' . '/predis/predis/src/Command/ListIndex.php',
        'Predis\\Command\\ListInsert' => __DIR__ . '/..' . '/predis/predis/src/Command/ListInsert.php',
        'Predis\\Command\\ListLength' => __DIR__ . '/..' . '/predis/predis/src/Command/ListLength.php',
        'Predis\\Command\\ListPopFirst' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPopFirst.php',
        'Predis\\Command\\ListPopFirstBlocking' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPopFirstBlocking.php',
        'Predis\\Command\\ListPopLast' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPopLast.php',
        'Predis\\Command\\ListPopLastBlocking' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPopLastBlocking.php',
        'Predis\\Command\\ListPopLastPushHead' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPopLastPushHead.php',
        'Predis\\Command\\ListPopLastPushHeadBlocking' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPopLastPushHeadBlocking.php',
        'Predis\\Command\\ListPushHead' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPushHead.php',
        'Predis\\Command\\ListPushHeadX' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPushHeadX.php',
        'Predis\\Command\\ListPushTail' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPushTail.php',
        'Predis\\Command\\ListPushTailX' => __DIR__ . '/..' . '/predis/predis/src/Command/ListPushTailX.php',
        'Predis\\Command\\ListRange' => __DIR__ . '/..' . '/predis/predis/src/Command/ListRange.php',
        'Predis\\Command\\ListRemove' => __DIR__ . '/..' . '/predis/predis/src/Command/ListRemove.php',
        'Predis\\Command\\ListSet' => __DIR__ . '/..' . '/predis/predis/src/Command/ListSet.php',
        'Predis\\Command\\ListTrim' => __DIR__ . '/..' . '/predis/predis/src/Command/ListTrim.php',
        'Predis\\Command\\PrefixableCommandInterface' => __DIR__ . '/..' . '/predis/predis/src/Command/PrefixableCommandInterface.php',
        'Predis\\Command\\Processor\\KeyPrefixProcessor' => __DIR__ . '/..' . '/predis/predis/src/Command/Processor/KeyPrefixProcessor.php',
        'Predis\\Command\\Processor\\ProcessorChain' => __DIR__ . '/..' . '/predis/predis/src/Command/Processor/ProcessorChain.php',
        'Predis\\Command\\Processor\\ProcessorInterface' => __DIR__ . '/..' . '/predis/predis/src/Command/Processor/ProcessorInterface.php',
        'Predis\\Command\\PubSubPublish' => __DIR__ . '/..' . '/predis/predis/src/Command/PubSubPublish.php',
        'Predis\\Command\\PubSubPubsub' => __DIR__ . '/..' . '/predis/predis/src/Command/PubSubPubsub.php',
        'Predis\\Command\\PubSubSubscribe' => __DIR__ . '/..' . '/predis/predis/src/Command/PubSubSubscribe.php',
        'Predis\\Command\\PubSubSubscribeByPattern' => __DIR__ . '/..' . '/predis/predis/src/Command/PubSubSubscribeByPattern.php',
        'Predis\\Command\\PubSubUnsubscribe' => __DIR__ . '/..' . '/predis/predis/src/Command/PubSubUnsubscribe.php',
        'Predis\\Command\\PubSubUnsubscribeByPattern' => __DIR__ . '/..' . '/predis/predis/src/Command/PubSubUnsubscribeByPattern.php',
        'Predis\\Command\\RawCommand' => __DIR__ . '/..' . '/predis/predis/src/Command/RawCommand.php',
        'Predis\\Command\\ScriptCommand' => __DIR__ . '/..' . '/predis/predis/src/Command/ScriptCommand.php',
        'Predis\\Command\\ServerBackgroundRewriteAOF' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerBackgroundRewriteAOF.php',
        'Predis\\Command\\ServerBackgroundSave' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerBackgroundSave.php',
        'Predis\\Command\\ServerClient' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerClient.php',
        'Predis\\Command\\ServerCommand' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerCommand.php',
        'Predis\\Command\\ServerConfig' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerConfig.php',
        'Predis\\Command\\ServerDatabaseSize' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerDatabaseSize.php',
        'Predis\\Command\\ServerEval' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerEval.php',
        'Predis\\Command\\ServerEvalSHA' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerEvalSHA.php',
        'Predis\\Command\\ServerFlushAll' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerFlushAll.php',
        'Predis\\Command\\ServerFlushDatabase' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerFlushDatabase.php',
        'Predis\\Command\\ServerInfo' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerInfo.php',
        'Predis\\Command\\ServerInfoV26x' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerInfoV26x.php',
        'Predis\\Command\\ServerLastSave' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerLastSave.php',
        'Predis\\Command\\ServerMonitor' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerMonitor.php',
        'Predis\\Command\\ServerObject' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerObject.php',
        'Predis\\Command\\ServerSave' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerSave.php',
        'Predis\\Command\\ServerScript' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerScript.php',
        'Predis\\Command\\ServerSentinel' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerSentinel.php',
        'Predis\\Command\\ServerShutdown' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerShutdown.php',
        'Predis\\Command\\ServerSlaveOf' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerSlaveOf.php',
        'Predis\\Command\\ServerSlowlog' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerSlowlog.php',
        'Predis\\Command\\ServerTime' => __DIR__ . '/..' . '/predis/predis/src/Command/ServerTime.php',
        'Predis\\Command\\SetAdd' => __DIR__ . '/..' . '/predis/predis/src/Command/SetAdd.php',
        'Predis\\Command\\SetCardinality' => __DIR__ . '/..' . '/predis/predis/src/Command/SetCardinality.php',
        'Predis\\Command\\SetDifference' => __DIR__ . '/..' . '/predis/predis/src/Command/SetDifference.php',
        'Predis\\Command\\SetDifferenceStore' => __DIR__ . '/..' . '/predis/predis/src/Command/SetDifferenceStore.php',
        'Predis\\Command\\SetIntersection' => __DIR__ . '/..' . '/predis/predis/src/Command/SetIntersection.php',
        'Predis\\Command\\SetIntersectionStore' => __DIR__ . '/..' . '/predis/predis/src/Command/SetIntersectionStore.php',
        'Predis\\Command\\SetIsMember' => __DIR__ . '/..' . '/predis/predis/src/Command/SetIsMember.php',
        'Predis\\Command\\SetMembers' => __DIR__ . '/..' . '/predis/predis/src/Command/SetMembers.php',
        'Predis\\Command\\SetMove' => __DIR__ . '/..' . '/predis/predis/src/Command/SetMove.php',
        'Predis\\Command\\SetPop' => __DIR__ . '/..' . '/predis/predis/src/Command/SetPop.php',
        'Predis\\Command\\SetRandomMember' => __DIR__ . '/..' . '/predis/predis/src/Command/SetRandomMember.php',
        'Predis\\Command\\SetRemove' => __DIR__ . '/..' . '/predis/predis/src/Command/SetRemove.php',
        'Predis\\Command\\SetScan' => __DIR__ . '/..' . '/predis/predis/src/Command/SetScan.php',
        'Predis\\Command\\SetUnion' => __DIR__ . '/..' . '/predis/predis/src/Command/SetUnion.php',
        'Predis\\Command\\SetUnionStore' => __DIR__ . '/..' . '/predis/predis/src/Command/SetUnionStore.php',
        'Predis\\Command\\StringAppend' => __DIR__ . '/..' . '/predis/predis/src/Command/StringAppend.php',
        'Predis\\Command\\StringBitCount' => __DIR__ . '/..' . '/predis/predis/src/Command/StringBitCount.php',
        'Predis\\Command\\StringBitField' => __DIR__ . '/..' . '/predis/predis/src/Command/StringBitField.php',
        'Predis\\Command\\StringBitOp' => __DIR__ . '/..' . '/predis/predis/src/Command/StringBitOp.php',
        'Predis\\Command\\StringBitPos' => __DIR__ . '/..' . '/predis/predis/src/Command/StringBitPos.php',
        'Predis\\Command\\StringDecrement' => __DIR__ . '/..' . '/predis/predis/src/Command/StringDecrement.php',
        'Predis\\Command\\StringDecrementBy' => __DIR__ . '/..' . '/predis/predis/src/Command/StringDecrementBy.php',
        'Predis\\Command\\StringGet' => __DIR__ . '/..' . '/predis/predis/src/Command/StringGet.php',
        'Predis\\Command\\StringGetBit' => __DIR__ . '/..' . '/predis/predis/src/Command/StringGetBit.php',
        'Predis\\Command\\StringGetMultiple' => __DIR__ . '/..' . '/predis/predis/src/Command/StringGetMultiple.php',
        'Predis\\Command\\StringGetRange' => __DIR__ . '/..' . '/predis/predis/src/Command/StringGetRange.php',
        'Predis\\Command\\StringGetSet' => __DIR__ . '/..' . '/predis/predis/src/Command/StringGetSet.php',
        'Predis\\Command\\StringIncrement' => __DIR__ . '/..' . '/predis/predis/src/Command/StringIncrement.php',
        'Predis\\Command\\StringIncrementBy' => __DIR__ . '/..' . '/predis/predis/src/Command/StringIncrementBy.php',
        'Predis\\Command\\StringIncrementByFloat' => __DIR__ . '/..' . '/predis/predis/src/Command/StringIncrementByFloat.php',
        'Predis\\Command\\StringPreciseSetExpire' => __DIR__ . '/..' . '/predis/predis/src/Command/StringPreciseSetExpire.php',
        'Predis\\Command\\StringSet' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSet.php',
        'Predis\\Command\\StringSetBit' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSetBit.php',
        'Predis\\Command\\StringSetExpire' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSetExpire.php',
        'Predis\\Command\\StringSetMultiple' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSetMultiple.php',
        'Predis\\Command\\StringSetMultiplePreserve' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSetMultiplePreserve.php',
        'Predis\\Command\\StringSetPreserve' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSetPreserve.php',
        'Predis\\Command\\StringSetRange' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSetRange.php',
        'Predis\\Command\\StringStrlen' => __DIR__ . '/..' . '/predis/predis/src/Command/StringStrlen.php',
        'Predis\\Command\\StringSubstr' => __DIR__ . '/..' . '/predis/predis/src/Command/StringSubstr.php',
        'Predis\\Command\\TransactionDiscard' => __DIR__ . '/..' . '/predis/predis/src/Command/TransactionDiscard.php',
        'Predis\\Command\\TransactionExec' => __DIR__ . '/..' . '/predis/predis/src/Command/TransactionExec.php',
        'Predis\\Command\\TransactionMulti' => __DIR__ . '/..' . '/predis/predis/src/Command/TransactionMulti.php',
        'Predis\\Command\\TransactionUnwatch' => __DIR__ . '/..' . '/predis/predis/src/Command/TransactionUnwatch.php',
        'Predis\\Command\\TransactionWatch' => __DIR__ . '/..' . '/predis/predis/src/Command/TransactionWatch.php',
        'Predis\\Command\\ZSetAdd' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetAdd.php',
        'Predis\\Command\\ZSetCardinality' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetCardinality.php',
        'Predis\\Command\\ZSetCount' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetCount.php',
        'Predis\\Command\\ZSetIncrementBy' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetIncrementBy.php',
        'Predis\\Command\\ZSetIntersectionStore' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetIntersectionStore.php',
        'Predis\\Command\\ZSetLexCount' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetLexCount.php',
        'Predis\\Command\\ZSetRange' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRange.php',
        'Predis\\Command\\ZSetRangeByLex' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRangeByLex.php',
        'Predis\\Command\\ZSetRangeByScore' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRangeByScore.php',
        'Predis\\Command\\ZSetRank' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRank.php',
        'Predis\\Command\\ZSetRemove' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRemove.php',
        'Predis\\Command\\ZSetRemoveRangeByLex' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRemoveRangeByLex.php',
        'Predis\\Command\\ZSetRemoveRangeByRank' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRemoveRangeByRank.php',
        'Predis\\Command\\ZSetRemoveRangeByScore' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetRemoveRangeByScore.php',
        'Predis\\Command\\ZSetReverseRange' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetReverseRange.php',
        'Predis\\Command\\ZSetReverseRangeByLex' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetReverseRangeByLex.php',
        'Predis\\Command\\ZSetReverseRangeByScore' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetReverseRangeByScore.php',
        'Predis\\Command\\ZSetReverseRank' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetReverseRank.php',
        'Predis\\Command\\ZSetScan' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetScan.php',
        'Predis\\Command\\ZSetScore' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetScore.php',
        'Predis\\Command\\ZSetUnionStore' => __DIR__ . '/..' . '/predis/predis/src/Command/ZSetUnionStore.php',
        'Predis\\CommunicationException' => __DIR__ . '/..' . '/predis/predis/src/CommunicationException.php',
        'Predis\\Configuration\\ClusterOption' => __DIR__ . '/..' . '/predis/predis/src/Configuration/ClusterOption.php',
        'Predis\\Configuration\\ConnectionFactoryOption' => __DIR__ . '/..' . '/predis/predis/src/Configuration/ConnectionFactoryOption.php',
        'Predis\\Configuration\\ExceptionsOption' => __DIR__ . '/..' . '/predis/predis/src/Configuration/ExceptionsOption.php',
        'Predis\\Configuration\\OptionInterface' => __DIR__ . '/..' . '/predis/predis/src/Configuration/OptionInterface.php',
        'Predis\\Configuration\\Options' => __DIR__ . '/..' . '/predis/predis/src/Configuration/Options.php',
        'Predis\\Configuration\\OptionsInterface' => __DIR__ . '/..' . '/predis/predis/src/Configuration/OptionsInterface.php',
        'Predis\\Configuration\\PrefixOption' => __DIR__ . '/..' . '/predis/predis/src/Configuration/PrefixOption.php',
        'Predis\\Configuration\\ProfileOption' => __DIR__ . '/..' . '/predis/predis/src/Configuration/ProfileOption.php',
        'Predis\\Configuration\\ReplicationOption' => __DIR__ . '/..' . '/predis/predis/src/Configuration/ReplicationOption.php',
        'Predis\\Connection\\AbstractConnection' => __DIR__ . '/..' . '/predis/predis/src/Connection/AbstractConnection.php',
        'Predis\\Connection\\AggregateConnectionInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/AggregateConnectionInterface.php',
        'Predis\\Connection\\Aggregate\\ClusterInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/Aggregate/ClusterInterface.php',
        'Predis\\Connection\\Aggregate\\MasterSlaveReplication' => __DIR__ . '/..' . '/predis/predis/src/Connection/Aggregate/MasterSlaveReplication.php',
        'Predis\\Connection\\Aggregate\\PredisCluster' => __DIR__ . '/..' . '/predis/predis/src/Connection/Aggregate/PredisCluster.php',
        'Predis\\Connection\\Aggregate\\RedisCluster' => __DIR__ . '/..' . '/predis/predis/src/Connection/Aggregate/RedisCluster.php',
        'Predis\\Connection\\Aggregate\\ReplicationInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/Aggregate/ReplicationInterface.php',
        'Predis\\Connection\\Aggregate\\SentinelReplication' => __DIR__ . '/..' . '/predis/predis/src/Connection/Aggregate/SentinelReplication.php',
        'Predis\\Connection\\CompositeConnectionInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/CompositeConnectionInterface.php',
        'Predis\\Connection\\CompositeStreamConnection' => __DIR__ . '/..' . '/predis/predis/src/Connection/CompositeStreamConnection.php',
        'Predis\\Connection\\ConnectionException' => __DIR__ . '/..' . '/predis/predis/src/Connection/ConnectionException.php',
        'Predis\\Connection\\ConnectionInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/ConnectionInterface.php',
        'Predis\\Connection\\Factory' => __DIR__ . '/..' . '/predis/predis/src/Connection/Factory.php',
        'Predis\\Connection\\FactoryInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/FactoryInterface.php',
        'Predis\\Connection\\NodeConnectionInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/NodeConnectionInterface.php',
        'Predis\\Connection\\Parameters' => __DIR__ . '/..' . '/predis/predis/src/Connection/Parameters.php',
        'Predis\\Connection\\ParametersInterface' => __DIR__ . '/..' . '/predis/predis/src/Connection/ParametersInterface.php',
        'Predis\\Connection\\PhpiredisSocketConnection' => __DIR__ . '/..' . '/predis/predis/src/Connection/PhpiredisSocketConnection.php',
        'Predis\\Connection\\PhpiredisStreamConnection' => __DIR__ . '/..' . '/predis/predis/src/Connection/PhpiredisStreamConnection.php',
        'Predis\\Connection\\StreamConnection' => __DIR__ . '/..' . '/predis/predis/src/Connection/StreamConnection.php',
        'Predis\\Connection\\WebdisConnection' => __DIR__ . '/..' . '/predis/predis/src/Connection/WebdisConnection.php',
        'Predis\\Monitor\\Consumer' => __DIR__ . '/..' . '/predis/predis/src/Monitor/Consumer.php',
        'Predis\\NotSupportedException' => __DIR__ . '/..' . '/predis/predis/src/NotSupportedException.php',
        'Predis\\Pipeline\\Atomic' => __DIR__ . '/..' . '/predis/predis/src/Pipeline/Atomic.php',
        'Predis\\Pipeline\\ConnectionErrorProof' => __DIR__ . '/..' . '/predis/predis/src/Pipeline/ConnectionErrorProof.php',
        'Predis\\Pipeline\\FireAndForget' => __DIR__ . '/..' . '/predis/predis/src/Pipeline/FireAndForget.php',
        'Predis\\Pipeline\\Pipeline' => __DIR__ . '/..' . '/predis/predis/src/Pipeline/Pipeline.php',
        'Predis\\PredisException' => __DIR__ . '/..' . '/predis/predis/src/PredisException.php',
        'Predis\\Profile\\Factory' => __DIR__ . '/..' . '/predis/predis/src/Profile/Factory.php',
        'Predis\\Profile\\ProfileInterface' => __DIR__ . '/..' . '/predis/predis/src/Profile/ProfileInterface.php',
        'Predis\\Profile\\RedisProfile' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisProfile.php',
        'Predis\\Profile\\RedisUnstable' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisUnstable.php',
        'Predis\\Profile\\RedisVersion200' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisVersion200.php',
        'Predis\\Profile\\RedisVersion220' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisVersion220.php',
        'Predis\\Profile\\RedisVersion240' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisVersion240.php',
        'Predis\\Profile\\RedisVersion260' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisVersion260.php',
        'Predis\\Profile\\RedisVersion280' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisVersion280.php',
        'Predis\\Profile\\RedisVersion300' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisVersion300.php',
        'Predis\\Profile\\RedisVersion320' => __DIR__ . '/..' . '/predis/predis/src/Profile/RedisVersion320.php',
        'Predis\\Protocol\\ProtocolException' => __DIR__ . '/..' . '/predis/predis/src/Protocol/ProtocolException.php',
        'Predis\\Protocol\\ProtocolProcessorInterface' => __DIR__ . '/..' . '/predis/predis/src/Protocol/ProtocolProcessorInterface.php',
        'Predis\\Protocol\\RequestSerializerInterface' => __DIR__ . '/..' . '/predis/predis/src/Protocol/RequestSerializerInterface.php',
        'Predis\\Protocol\\ResponseReaderInterface' => __DIR__ . '/..' . '/predis/predis/src/Protocol/ResponseReaderInterface.php',
        'Predis\\Protocol\\Text\\CompositeProtocolProcessor' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/CompositeProtocolProcessor.php',
        'Predis\\Protocol\\Text\\Handler\\BulkResponse' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/Handler/BulkResponse.php',
        'Predis\\Protocol\\Text\\Handler\\ErrorResponse' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/Handler/ErrorResponse.php',
        'Predis\\Protocol\\Text\\Handler\\IntegerResponse' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/Handler/IntegerResponse.php',
        'Predis\\Protocol\\Text\\Handler\\MultiBulkResponse' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/Handler/MultiBulkResponse.php',
        'Predis\\Protocol\\Text\\Handler\\ResponseHandlerInterface' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/Handler/ResponseHandlerInterface.php',
        'Predis\\Protocol\\Text\\Handler\\StatusResponse' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/Handler/StatusResponse.php',
        'Predis\\Protocol\\Text\\Handler\\StreamableMultiBulkResponse' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/Handler/StreamableMultiBulkResponse.php',
        'Predis\\Protocol\\Text\\ProtocolProcessor' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/ProtocolProcessor.php',
        'Predis\\Protocol\\Text\\RequestSerializer' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/RequestSerializer.php',
        'Predis\\Protocol\\Text\\ResponseReader' => __DIR__ . '/..' . '/predis/predis/src/Protocol/Text/ResponseReader.php',
        'Predis\\PubSub\\AbstractConsumer' => __DIR__ . '/..' . '/predis/predis/src/PubSub/AbstractConsumer.php',
        'Predis\\PubSub\\Consumer' => __DIR__ . '/..' . '/predis/predis/src/PubSub/Consumer.php',
        'Predis\\PubSub\\DispatcherLoop' => __DIR__ . '/..' . '/predis/predis/src/PubSub/DispatcherLoop.php',
        'Predis\\Replication\\MissingMasterException' => __DIR__ . '/..' . '/predis/predis/src/Replication/MissingMasterException.php',
        'Predis\\Replication\\ReplicationStrategy' => __DIR__ . '/..' . '/predis/predis/src/Replication/ReplicationStrategy.php',
        'Predis\\Replication\\RoleException' => __DIR__ . '/..' . '/predis/predis/src/Replication/RoleException.php',
        'Predis\\Response\\Error' => __DIR__ . '/..' . '/predis/predis/src/Response/Error.php',
        'Predis\\Response\\ErrorInterface' => __DIR__ . '/..' . '/predis/predis/src/Response/ErrorInterface.php',
        'Predis\\Response\\Iterator\\MultiBulk' => __DIR__ . '/..' . '/predis/predis/src/Response/Iterator/MultiBulk.php',
        'Predis\\Response\\Iterator\\MultiBulkIterator' => __DIR__ . '/..' . '/predis/predis/src/Response/Iterator/MultiBulkIterator.php',
        'Predis\\Response\\Iterator\\MultiBulkTuple' => __DIR__ . '/..' . '/predis/predis/src/Response/Iterator/MultiBulkTuple.php',
        'Predis\\Response\\ResponseInterface' => __DIR__ . '/..' . '/predis/predis/src/Response/ResponseInterface.php',
        'Predis\\Response\\ServerException' => __DIR__ . '/..' . '/predis/predis/src/Response/ServerException.php',
        'Predis\\Response\\Status' => __DIR__ . '/..' . '/predis/predis/src/Response/Status.php',
        'Predis\\Session\\Handler' => __DIR__ . '/..' . '/predis/predis/src/Session/Handler.php',
        'Predis\\Transaction\\AbortedMultiExecException' => __DIR__ . '/..' . '/predis/predis/src/Transaction/AbortedMultiExecException.php',
        'Predis\\Transaction\\MultiExec' => __DIR__ . '/..' . '/predis/predis/src/Transaction/MultiExec.php',
        'Predis\\Transaction\\MultiExecState' => __DIR__ . '/..' . '/predis/predis/src/Transaction/MultiExecState.php',
        'SevenFields\\Bootstrap\\Bootstrap' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Bootstrap/Bootstrap.php',
        'SevenFields\\Container\\Container' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Container/Container.php',
        'SevenFields\\Fields\\Checkbox' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Checkbox.php',
        'SevenFields\\Fields\\Fields' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Fields.php',
        'SevenFields\\Fields\\Header' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Header.php',
        'SevenFields\\Fields\\Html' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Html.php',
        'SevenFields\\Fields\\Multiselect' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Multiselect.php',
        'SevenFields\\Fields\\Password' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Password.php',
        'SevenFields\\Fields\\Select' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Select.php',
        'SevenFields\\Fields\\Text' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Text.php',
        'SevenFields\\Fields\\Textarea' => __DIR__ . '/..' . '/foadyousefi/seven-fields/src/Fields/Textarea.php',
        'Vaites\\ApacheTika\\Client' => __DIR__ . '/..' . '/vaites/php-apache-tika/src/Client.php',
        'Vaites\\ApacheTika\\Clients\\CLIClient' => __DIR__ . '/..' . '/vaites/php-apache-tika/src/Clients/CLIClient.php',
        'Vaites\\ApacheTika\\Clients\\WebClient' => __DIR__ . '/..' . '/vaites/php-apache-tika/src/Clients/WebClient.php',
        'Vaites\\ApacheTika\\Metadata\\DocumentMetadata' => __DIR__ . '/..' . '/vaites/php-apache-tika/src/Metadata/DocumentMetadata.php',
        'Vaites\\ApacheTika\\Metadata\\ImageMetadata' => __DIR__ . '/..' . '/vaites/php-apache-tika/src/Metadata/ImageMetadata.php',
        'Vaites\\ApacheTika\\Metadata\\Metadata' => __DIR__ . '/..' . '/vaites/php-apache-tika/src/Metadata/Metadata.php',
        'Vaites\\ApacheTika\\Metadata\\MetadataInterface' => __DIR__ . '/..' . '/vaites/php-apache-tika/src/Metadata/MetadataInterface.php',
        'WpRediSearch\\Admin\\Admin' => __DIR__ . '/../..' . '/src/Admin/Admin.php',
        'WpRediSearch\\Feature' => __DIR__ . '/../..' . '/src/Feature.php',
        'WpRediSearch\\Features' => __DIR__ . '/../..' . '/src/Features.php',
        'WpRediSearch\\Features\\Document' => __DIR__ . '/../..' . '/src/Features/Document.php',
        'WpRediSearch\\Features\\LiveSearch\\LiveSearch' => __DIR__ . '/../..' . '/src/Features/LiveSearch/LiveSearch.php',
        'WpRediSearch\\Features\\Synonym' => __DIR__ . '/../..' . '/src/Features/Synonym.php',
        'WpRediSearch\\Features\\WooCommerce' => __DIR__ . '/../..' . '/src/Features/WooCommerce.php',
        'WpRediSearch\\RediSearch\\Client' => __DIR__ . '/../..' . '/src/RediSearch/Client.php',
        'WpRediSearch\\RediSearch\\Index' => __DIR__ . '/../..' . '/src/RediSearch/Index.php',
        'WpRediSearch\\RediSearch\\Search' => __DIR__ . '/../..' . '/src/RediSearch/Search.php',
        'WpRediSearch\\RedisRaw\\AbstractRedisRawClient' => __DIR__ . '/../..' . '/src/RedisRaw/AbstractRedisRawClient.php',
        'WpRediSearch\\RedisRaw\\PredisAdapter' => __DIR__ . '/../..' . '/src/RedisRaw/PredisAdapter.php',
        'WpRediSearch\\RedisRaw\\RedisRawClientInterface' => __DIR__ . '/../..' . '/src/RedisRaw/RedisRawClientInterface.php',
        'WpRediSearch\\Settings' => __DIR__ . '/../..' . '/src/Settings.php',
        'WpRediSearch\\Utils\\MsOfficeParser' => __DIR__ . '/../..' . '/src/Utils/MsOfficeParser.php',
        'WpRediSearch\\WpRediSearch' => __DIR__ . '/../..' . '/src/WpRediSearch.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd0d9ec57d4a198e8c0a84af8a5284dcb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd0d9ec57d4a198e8c0a84af8a5284dcb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd0d9ec57d4a198e8c0a84af8a5284dcb::$classMap;

        }, null, ClassLoader::class);
    }
}

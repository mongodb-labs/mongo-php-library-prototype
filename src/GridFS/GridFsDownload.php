<?php
namespace MongoDB\GridFS;

use MongoDB\Collection;
use MongoDB\Exception\RuntimeException;
use MongoDB\BSON\ObjectId;
/**
 * GridFSDownload abstracts the processes of downloading from a GridFSBucket
 *
 */
class GridFsDownload
{
    private $chunksIterator;
    private $bytesSeen=0;
    private $numChunks;
    private $iteratorEmpty=false;
    private $firstCheck=true;
    private $bufferFresh=true;
    private $bufferEmpty=true;
    private $collectionsWrapper;
    private $chunkOffset = 0;
    private $buffer;
    private $file;
    /**
     * Constructs a GridFS download stream
     *
     *
     * @param GridFSCollectionsWrapper $collectionsWrapper   File options
     * @param \MongoDB\BSON\ObjectId   $options              File options
     * @param array                    $options              File options
     * @throws FileNotFoundException
     */
    public function __construct(
        GridFSCollectionsWrapper $collectionsWrapper,
        $file
        )
    {
        $this->collectionsWrapper = $collectionsWrapper;
        $this->file = $file;
        $cursor = $this->collectionsWrapper->getChunksCollection()->find(['files_id' => $this->file->_id], ['sort' => ['n' => 1]]);
        $this->chunksIterator = new \IteratorIterator($cursor);
        if ($this->file->length >= 0) {
            $this->numChunks = ceil($this->file->length / $this->file->chunkSize);
        } else {
            $this->numChunks = 0;
        }
        $this->buffer = fopen('php://temp', 'w+');
    }
    /**
    * Reads data from a stream into GridFS
    *
    * @param Stream   $source   Source Stream
    * @return ObjectId
    */
    public function downloadToStream($destination)
    {
        while($this->advanceChunks()) {
            fwrite($destination, $this->chunksIterator->current()->data->getData());
        }
    }

    public function downloadNumBytes($numToRead) {
        $output = "";
        if ($this->bufferFresh) {
            rewind($this->buffer);
            $this->bufferFresh=false;
        }

        $output = fread($this->buffer, $numToRead);
        if (strlen($output) == $numToRead) {
            return $output;
        }
        fclose($this->buffer);
        $this->buffer = fopen("php://temp", "w+");

        $this->bufferFresh=true;
        $this->bufferEmpty=true;

        $bytesLeft = $numToRead - strlen($output);

        while(strlen($output) < $numToRead && $this->advanceChunks()) {
            $bytesLeft = $numToRead - strlen($output);
            $output .= substr($this->chunksIterator->current()->data->getData(), 0, $bytesLeft);
        }
        if ($this->file->length > 0 && $bytesLeft < strlen($this->chunksIterator->current()->data->getData())) {
            fwrite($this->buffer, substr($this->chunksIterator->current()->data->getData(), $bytesLeft));
            $this->bufferEmpty=false;
        }
        return $output;
    }
    public function getSize()
    {
        return $this->file->length;
    }
    public function getId()
    {
        return $this->file->_id;
    }
    private function advanceChunks()
    {
        if($this->chunkOffset >= $this->numChunks) {
            $this->iteratorEmpty=true;
            return false;
        }
        if($this->firstCheck) {
            $this->chunksIterator->rewind();
            $this->firstCheck=false;
        } else {
            $this->chunksIterator->next();
        }
        if (!$this->chunksIterator->valid()) {
            throw new \MongoDB\Exception\GridFSCorruptFileException();
        }
        if ($this->chunksIterator->current()->n != $this->chunkOffset) {
            throw new \MongoDB\Exception\GridFSCorruptFileException();
        }
        $chunkSizeIs = strlen($this->chunksIterator->current()->data->getData());
        if ($this->chunkOffset == $this->numChunks - 1) {
            $chunkSizeShouldBe = $this->file->length - $this->bytesSeen;
            if($chunkSizeShouldBe != $chunkSizeIs) {
                throw new \MongoDB\Exception\GridFSCorruptFileException();
            }
        } else if ($this->chunkOffset < $this->numChunks - 1) {
            if($chunkSizeIs != $this->file->chunkSize) {
                throw new \MongoDB\Exception\GridFSCorruptFileException();
            }
        }
        $this->bytesSeen+= $chunkSizeIs;
        $this->chunkOffset++;
        return true;
    }
    public function close()
    {
        fclose($this->buffer);
    }

    public function isEOF()
    {
        $eof = $this->iteratorEmpty && $this->bufferEmpty;
        return $eof;
    }
}

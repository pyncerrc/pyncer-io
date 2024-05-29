<?php
namespace Pyncer\IO\File;

enum FileMode: int
{
    case READ = 1;
    case WRITE = 2;
    case READ_WRITE = 3;
}

<?php

declare (strict_types=1);
namespace Rector\Privatization\NodeManipulator;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Rector\Core\Exception\InvalidNodeTypeException;
use Rector\Core\ValueObject\Visibility;
use RectorPrefix20210710\Webmozart\Assert\Assert;
final class VisibilityManipulator
{
    /**
     * @var array<class-string<Stmt>>
     */
    private const ALLOWED_NODE_TYPES = [\PhpParser\Node\Stmt\ClassMethod::class, \PhpParser\Node\Stmt\Property::class, \PhpParser\Node\Stmt\ClassConst::class, \PhpParser\Node\Stmt\Class_::class];
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function makeStatic($node) : void
    {
        $this->addVisibilityFlag($node, \Rector\Core\ValueObject\Visibility::STATIC);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Class_ $node
     */
    public function makeAbstract($node) : void
    {
        $this->addVisibilityFlag($node, \Rector\Core\ValueObject\Visibility::ABSTRACT);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property $node
     */
    public function makeNonStatic($node) : void
    {
        if (!$node->isStatic()) {
            return;
        }
        $node->flags -= \PhpParser\Node\Stmt\Class_::MODIFIER_STATIC;
    }
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod $node
     */
    public function makeFinal($node) : void
    {
        $this->addVisibilityFlag($node, \Rector\Core\ValueObject\Visibility::FINAL);
    }
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod $node
     */
    public function makeNonFinal($node) : void
    {
        if (!$node->isFinal()) {
            return;
        }
        $node->flags -= \PhpParser\Node\Stmt\Class_::MODIFIER_FINAL;
    }
    /**
     * This way "abstract", "static", "final" are kept
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function removeVisibility($node) : void
    {
        $this->ensureIsClassMethodOrProperty($node, __METHOD__);
        // no modifier
        if ($node->flags === 0) {
            return;
        }
        if ($node->isPublic()) {
            $node->flags -= \PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC;
        }
        if ($node->isProtected()) {
            $node->flags -= \PhpParser\Node\Stmt\Class_::MODIFIER_PROTECTED;
        }
        if ($node->isPrivate()) {
            $node->flags -= \PhpParser\Node\Stmt\Class_::MODIFIER_PRIVATE;
        }
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function changeNodeVisibility($node, int $visibility) : void
    {
        \RectorPrefix20210710\Webmozart\Assert\Assert::oneOf($visibility, [\Rector\Core\ValueObject\Visibility::PUBLIC, \Rector\Core\ValueObject\Visibility::PROTECTED, \Rector\Core\ValueObject\Visibility::PRIVATE, \Rector\Core\ValueObject\Visibility::STATIC, \Rector\Core\ValueObject\Visibility::ABSTRACT, \Rector\Core\ValueObject\Visibility::FINAL]);
        $this->replaceVisibilityFlag($node, $visibility);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function makePublic($node) : void
    {
        $this->replaceVisibilityFlag($node, \Rector\Core\ValueObject\Visibility::PUBLIC);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function makeProtected($node) : void
    {
        $this->replaceVisibilityFlag($node, \Rector\Core\ValueObject\Visibility::PROTECTED);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function makePrivate($node) : void
    {
        $this->replaceVisibilityFlag($node, \Rector\Core\ValueObject\Visibility::PRIVATE);
    }
    public function removeFinal(\PhpParser\Node\Stmt\Class_ $class) : void
    {
        $class->flags -= \PhpParser\Node\Stmt\Class_::MODIFIER_FINAL;
    }
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    private function addVisibilityFlag($node, int $visibility) : void
    {
        $this->ensureIsClassMethodOrProperty($node, __METHOD__);
        $node->flags |= $visibility;
    }
    private function ensureIsClassMethodOrProperty(\PhpParser\Node $node, string $location) : void
    {
        foreach (self::ALLOWED_NODE_TYPES as $allowedNodeType) {
            if (\is_a($node, $allowedNodeType, \true)) {
                return;
            }
        }
        throw new \Rector\Core\Exception\InvalidNodeTypeException(\sprintf('"%s" only accepts "%s" types. "%s" given.', $location, \implode('", "', self::ALLOWED_NODE_TYPES), \get_class($node)));
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    private function replaceVisibilityFlag($node, int $visibility) : void
    {
        $isStatic = $node instanceof \PhpParser\Node\Stmt\ClassMethod && $node->isStatic();
        if ($isStatic) {
            $this->removeVisibility($node);
        }
        if ($visibility !== \Rector\Core\ValueObject\Visibility::STATIC && $visibility !== \Rector\Core\ValueObject\Visibility::ABSTRACT && $visibility !== \Rector\Core\ValueObject\Visibility::FINAL) {
            $this->removeVisibility($node);
        }
        $this->addVisibilityFlag($node, $visibility);
        if ($isStatic) {
            $this->makeStatic($node);
        }
    }
}

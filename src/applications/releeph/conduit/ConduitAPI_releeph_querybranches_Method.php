<?php

final class ConduitAPI_releeph_querybranches_Method
  extends ConduitAPI_releeph_Method {

  public function getMethodDescription() {
    return pht('Query information about Releeph branches.');
  }

  public function defineParamTypes() {
    return array(
      'ids' => 'optional list<id>',
      'phids' => 'optional list<phid>',
      'productPHIDs' => 'optional list<phid>',
    ) + $this->getPagerParamTypes();
  }

  public function defineReturnType() {
    return 'query-results';
  }

  public function defineErrorTypes() {
    return array();
  }

  protected function execute(ConduitAPIRequest $request) {
    $viewer = $request->getUser();

    $query = id(new ReleephBranchQuery())
      ->setViewer($viewer);

    $ids = $request->getValue('ids');
    if ($ids !== null) {
      $query->withIDs($ids);
    }

    $phids = $request->getValue('phids');
    if ($phids !== null) {
      $query->withPHIDs($phids);
    }

    $product_phids = $request->getValue('productPHIDs');
    if ($product_phids !== null) {
      $query->withProductPHIDs($product_phids);
    }

    $pager = $this->newPager($request);
    $branches = $query->executeWithCursorPager($pager);

    $data = array();
    foreach ($branches as $branch) {
      $id = $branch->getID();

      $uri = '/releeph/branch/'.$id.'/';
      $uri = PhabricatorEnv::getProductionURI($uri);

      $data[] = array(
        'id' => $id,
        'phid' => $branch->getPHID(),
        'uri' => $uri,
        'name' => $branch->getName(),
        'productPHID' => $branch->getProduct()->getPHID(),
      );
    }

    return $this->addPagerResults(
      array(
        'data' => $data,
      ),
      $pager);
  }

}

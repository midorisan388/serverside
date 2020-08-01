


{
  testmap={
    name:"test",
    img_folder:"img/map/test",
    mapcsv:"img/map/test/areaid_map.csv",
    start_sel:[0,0],
    //map_img_uri:"img/map/test/tile.png",
    map_img_uri:"img/map/map1/ソフィア.png",//"img/map/map1/map1.jpg",
    map_img_wall:["img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg"],
    bringimg_uri:["img/map/test/brine0.png","img/map/test/brine1.png","img/map/test/brine2.png","img/map/test/brine3.png","img/map/test/brine3.png","img/map/map1/map1brine.png"],
    Area_data:  [
        [[0,20],[0,20]],
        [[21,40],[0,20]],
        [[0,20],[21,40]],
        [[21,40],[21,40]]
      ],
    Arealength:4,
    npcobj:
      new NPC("img/player.jpg",32,36,"NPC1")
  },
  map1={
    name:"test1",
    img_folder:"img/map/map1",
    mapcsv:"img/map/map1/areaid_map1.csv",
    start_sel:[19,19],
    map_img_uri:"img/map/map1/map1.jpg",
    map_img_wall:["img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg","img/map/map1/map1_5wall.jpg"],
    bringimg_uri:["img/map/map1/map1_0wall.jpg","img/map/map1/map1_1wall.jpg","img/map/map1/map1_2wall.jpg","img/map/map1/map1_3wall.jpg","img/map/map1/map1_4wall.jpg","img/map/map1/map1_5wall.jpg"],
    Area_data:  [
        [[3,37],[17,26]],
        [[5,12],[8,16]],
        [[15,22],[8,16]],
        [[26,32],[8,16]],
        [[5,21],[27,38]],
        [[22,21],[29,38]]
      ],
    Arealength:6,

    npcobj:
      new NPC("img/player.jpg",32,36,"NPC1")

  }

}
